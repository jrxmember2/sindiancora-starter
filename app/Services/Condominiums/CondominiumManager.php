<?php

namespace App\Services\Condominiums;

use App\Models\Company;
use App\Models\Condominium;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CondominiumManager
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected LicenseGuard $licenseGuard,
        protected CondominiumGovernanceManager $governanceManager,
    ) {
    }

    public function createOrRequestAccess(array $data, Company $company, ?User $actor = null): array
    {
        $documentDigits = $this->normalizeDocument($data['document'] ?? null);

        if ($documentDigits) {
            $existing = Condominium::query()
                ->withoutGlobalScopes()
                ->where('document_digits', $documentDigits)
                ->first();

            if ($existing) {
                $alreadyLinked = $existing->companyLinks()
                    ->where('company_id', $company->id)
                    ->where('status', 'active')
                    ->exists();

                if ($alreadyLinked) {
                    throw ValidationException::withMessages([
                        'document' => 'Este condomínio já está vinculado à empresa atual.',
                    ]);
                }

                $request = $this->governanceManager->requestLink($existing, $company, $actor ?? auth()->user());

                return [
                    'status' => 'requested',
                    'condominium' => $existing->fresh(),
                    'request' => $request,
                ];
            }
        }

        return [
            'status' => 'created',
            'condominium' => $this->create($data, $company, $actor),
            'request' => null,
        ];
    }

    public function create(array $data, Company $company, ?User $actor = null): Condominium
    {
        return DB::transaction(function () use ($data, $company, $actor) {
            $payload = $this->buildPayload($data);
            $payload['company_id'] = $company->id;

            $condominium = Condominium::query()->create($payload);

            $this->syncUsageSnapshots($condominium);
            $condominium->refresh();

            $this->auditLogger->record(
                action: 'condominium.created',
                actor: $actor,
                auditable: $condominium,
                company: $company,
                newValues: $this->snapshot($condominium),
            );

            return $condominium;
        });
    }

    public function update(Condominium $condominium, array $data, ?User $actor = null): Condominium
    {
        return DB::transaction(function () use ($condominium, $data, $actor) {
            $before = $this->snapshot($condominium);
            $payload = $this->buildPayload($data, $condominium);

            $duplicate = Condominium::query()
                ->withoutGlobalScopes()
                ->where('document_digits', $payload['document_digits'])
                ->where('id', '!=', $condominium->id)
                ->first();

            if ($payload['document_digits'] && $duplicate) {
                throw ValidationException::withMessages([
                    'document' => 'Já existe um condomínio canônico com este documento. Use o fluxo de vínculo compartilhado.',
                ]);
            }

            $condominium->update($payload);

            $this->syncUsageSnapshots($condominium);
            $condominium->refresh();

            $this->auditLogger->record(
                action: 'condominium.updated',
                actor: $actor,
                auditable: $condominium,
                company: $condominium->company,
                oldValues: $before,
                newValues: $this->snapshot($condominium),
            );

            return $condominium;
        });
    }

    public function inactivate(Condominium $condominium, ?User $actor = null): Condominium
    {
        return DB::transaction(function () use ($condominium, $actor) {
            $before = $this->snapshot($condominium);

            if ($condominium->status !== 'inactive') {
                $condominium->update(['status' => 'inactive']);
            }

            $this->syncUsageSnapshots($condominium);
            $condominium->refresh();

            $this->auditLogger->record(
                action: 'condominium.inactivated',
                actor: $actor,
                auditable: $condominium,
                company: $condominium->company,
                oldValues: $before,
                newValues: $this->snapshot($condominium),
            );

            return $condominium;
        });
    }

    protected function buildPayload(array $data, ?Condominium $condominium = null): array
    {
        $payload = collect($data)
            ->except(['logo', 'remove_logo'])
            ->all();
        $payload['document_digits'] = $this->normalizeDocument($data['document'] ?? null);

        $shouldRemoveLogo = (bool) ($data['remove_logo'] ?? false);
        $uploadedLogo = $data['logo'] ?? null;
        $currentLogoPath = $condominium?->getRawOriginal('logo_url');

        if ($shouldRemoveLogo && $currentLogoPath) {
            Storage::disk('public')->delete($currentLogoPath);
            $payload['logo_url'] = null;
        }

        if ($uploadedLogo instanceof UploadedFile) {
            if ($currentLogoPath) {
                Storage::disk('public')->delete($currentLogoPath);
            }

            $payload['logo_url'] = $uploadedLogo->store('condominiums/logos', 'public');
        } elseif (! array_key_exists('logo_url', $payload) && ! $shouldRemoveLogo && ! $condominium) {
            $payload['logo_url'] = null;
        }

        return $payload;
    }

    protected function snapshot(Condominium $condominium): array
    {
        return [
            'id' => $condominium->id,
            'company_id' => $condominium->company_id,
            'name' => $condominium->name,
            'document' => $condominium->document,
            'document_digits' => $condominium->document_digits,
            'email' => $condominium->email,
            'phone' => $condominium->phone,
            'status' => $condominium->status,
            'slug' => $condominium->slug,
            'city' => $condominium->city,
            'state' => $condominium->state,
            'administrator_name' => $condominium->administrator_name,
            'mandate_start' => $condominium->mandate_start?->toDateString(),
            'mandate_end' => $condominium->mandate_end?->toDateString(),
            'logo_url' => $condominium->getRawOriginal('logo_url'),
        ];
    }

    protected function normalizeDocument(?string $document): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $document);

        return $digits !== '' ? $digits : null;
    }

    protected function syncUsageSnapshots(Condominium $condominium): void
    {
        $condominium->loadMissing('companies:id');

        $condominium->companies
            ->filter(fn (Company $company) => $company->pivot?->status === 'active')
            ->each(fn (Company $company) => $this->licenseGuard->syncUsageSnapshot($company));
    }
}
