<?php

namespace App\Services\Condominiums;

use App\Models\Company;
use App\Models\CompanyCondominium;
use App\Models\Condominium;
use App\Models\CondominiumLinkRequest;
use App\Models\User;
use App\Notifications\CondominiumLinkWorkflowNotification;
use App\Services\Audit\AuditLogger;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CondominiumGovernanceManager
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected LicenseGuard $licenseGuard,
    ) {
    }

    public function requestLink(Condominium $condominium, Company $requestingCompany, User $requester, ?string $notes = null): CondominiumLinkRequest
    {
        if ($condominium->companyLinks()
            ->where('company_id', $requestingCompany->id)
            ->where('status', 'active')
            ->exists()) {
            throw ValidationException::withMessages([
                'document' => 'Este condomínio já está vinculado à empresa ativa no momento.',
            ]);
        }

        $existingRequest = CondominiumLinkRequest::query()
            ->where('condominium_id', $condominium->id)
            ->where('requesting_company_id', $requestingCompany->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return $existingRequest;
        }

        $request = CondominiumLinkRequest::query()->create([
            'condominium_id' => $condominium->id,
            'requesting_company_id' => $requestingCompany->id,
            'current_primary_company_id' => $condominium->company_id,
            'requested_by_user_id' => $requester->id,
            'status' => 'pending',
            'request_notes' => $notes,
        ]);

        foreach ($this->primaryRecipients($condominium->company) as $recipient) {
            $recipient->notify(new CondominiumLinkWorkflowNotification(
                title: 'Nova solicitação de vínculo de condomínio',
                message: "A empresa {$requestingCompany->name} solicitou acesso ao condomínio {$condominium->name}.",
                actionUrl: '/app/condominium-link-requests',
                kind: 'warning',
            ));
        }

        $this->auditLogger->record(
            action: 'condominium.link_requested',
            actor: $requester,
            auditable: $request,
            company: $requestingCompany,
            newValues: $this->requestSnapshot($request),
        );

        return $request;
    }

    public function share(CondominiumLinkRequest $request, User $actor, ?string $notes = null): CondominiumLinkRequest
    {
        return DB::transaction(function () use ($request, $actor, $notes) {
            $this->ensurePending($request);
            $request->loadMissing(['condominium', 'requestingCompany', 'currentPrimaryCompany']);
            $this->ensureTargetCompanyCapacity($request);

            CompanyCondominium::query()->updateOrCreate(
                [
                    'company_id' => $request->requesting_company_id,
                    'condominium_id' => $request->condominium_id,
                ],
                [
                    'relationship_type' => 'solidaria',
                    'status' => 'active',
                    'linked_by_user_id' => $request->requested_by_user_id,
                    'approved_by_user_id' => $actor->id,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'notes' => $notes,
                ]
            );

            $request->update([
                'status' => 'shared',
                'decision_type' => 'mesclar',
                'responded_by_user_id' => $actor->id,
                'resolved_at' => now(),
                'decision_notes' => $notes,
            ]);

            $this->licenseGuard->syncUsageSnapshot($request->requestingCompany);

            $this->notifyRequestingCompany(
                $request,
                'Solicitação aprovada em modo solidário',
                "O condomínio {$request->condominium->name} agora está compartilhado com a sua empresa.",
                'success'
            );

            $this->auditLogger->record(
                action: 'condominium.link_shared',
                actor: $actor,
                auditable: $request,
                company: $request->requestingCompany,
                newValues: $this->requestSnapshot($request->fresh()),
            );

            return $request->fresh();
        });
    }

    public function transfer(CondominiumLinkRequest $request, User $actor, ?string $notes = null, bool $forced = false): CondominiumLinkRequest
    {
        return DB::transaction(function () use ($request, $actor, $notes, $forced) {
            $this->ensurePending($request);
            $request->loadMissing(['condominium', 'requestingCompany', 'currentPrimaryCompany']);

            if (! $forced) {
                $this->ensureTargetCompanyCapacity($request);
            }

            $condominium = $request->condominium;
            $previousPrimaryCompany = $condominium->company;

            $condominium->companyLinks()
                ->where('company_id', $previousPrimaryCompany?->id)
                ->where('relationship_type', 'principal')
                ->where('status', 'active')
                ->update([
                    'status' => 'transferred',
                    'ends_at' => now(),
                    'approved_by_user_id' => $actor->id,
                    'notes' => $notes,
                    'updated_at' => now(),
                ]);

            CompanyCondominium::query()->updateOrCreate(
                [
                    'company_id' => $request->requesting_company_id,
                    'condominium_id' => $request->condominium_id,
                ],
                [
                    'relationship_type' => 'principal',
                    'status' => 'active',
                    'linked_by_user_id' => $request->requested_by_user_id,
                    'approved_by_user_id' => $actor->id,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'notes' => $notes,
                ]
            );

            $condominium->update(['company_id' => $request->requesting_company_id]);

            $request->update([
                'status' => 'transferred',
                'decision_type' => 'transferir',
                'responded_by_user_id' => $actor->id,
                'resolved_at' => now(),
                'decision_notes' => $notes,
            ]);

            $this->licenseGuard->syncUsageSnapshot($previousPrimaryCompany);
            $this->licenseGuard->syncUsageSnapshot($request->requestingCompany);

            $this->notifyRequestingCompany(
                $request,
                'Transferência de condomínio aprovada',
                "O condomínio {$condominium->name} foi transferido para a sua empresa.",
                'success'
            );

            foreach ($this->primaryRecipients($previousPrimaryCompany) as $recipient) {
                $recipient->notify(new CondominiumLinkWorkflowNotification(
                    title: $forced ? 'Transferência aplicada pelo superadmin' : 'Transferência de condomínio concluída',
                    message: "O condomínio {$condominium->name} deixou de ser a gestão principal da sua empresa.",
                    actionUrl: '/app/condominium-link-requests',
                    kind: 'warning',
                ));
            }

            $this->auditLogger->record(
                action: $forced ? 'condominium.link_transferred_by_superadmin' : 'condominium.link_transferred',
                actor: $actor,
                auditable: $request,
                company: $request->requestingCompany,
                newValues: $this->requestSnapshot($request->fresh()),
            );

            return $request->fresh();
        });
    }

    public function reject(CondominiumLinkRequest $request, User $actor, ?string $notes = null): CondominiumLinkRequest
    {
        $this->ensurePending($request);

        $request->update([
            'status' => 'rejected',
            'decision_type' => 'recusar',
            'responded_by_user_id' => $actor->id,
            'resolved_at' => now(),
            'decision_notes' => $notes,
        ]);

        $request->loadMissing(['condominium', 'requestingCompany']);

        $this->notifyRequestingCompany(
            $request,
            'Solicitação de vínculo recusada',
            "A solicitação da sua empresa para o condomínio {$request->condominium->name} foi recusada.",
            'error'
        );

        $this->auditLogger->record(
            action: 'condominium.link_rejected',
            actor: $actor,
            auditable: $request,
            company: $request->requestingCompany,
            newValues: $this->requestSnapshot($request->fresh()),
        );

        return $request->fresh();
    }

    public function forceTransferToCompany(Condominium $condominium, Company $targetCompany, User $superadmin, ?string $notes = null): CondominiumLinkRequest
    {
        $request = CondominiumLinkRequest::query()->create([
            'condominium_id' => $condominium->id,
            'requesting_company_id' => $targetCompany->id,
            'current_primary_company_id' => $condominium->company_id,
            'requested_by_user_id' => $superadmin->id,
            'status' => 'pending',
            'request_notes' => $notes,
        ]);

        return $this->transfer($request, $superadmin, $notes, true);
    }

    protected function notifyRequestingCompany(
        CondominiumLinkRequest $request,
        string $title,
        string $message,
        string $kind,
    ): void {
        foreach ($this->primaryRecipients($request->requestingCompany) as $recipient) {
            $recipient->notify(new CondominiumLinkWorkflowNotification(
                title: $title,
                message: $message,
                actionUrl: '/app/condominium-link-requests',
                kind: $kind,
            ));
        }
    }

    protected function primaryRecipients(?Company $company): Collection
    {
        if (! $company) {
            return collect();
        }

        $primaryUsers = $company->companyUsers()
            ->with('user')
            ->where('status', 'active')
            ->where('is_primary', true)
            ->get()
            ->pluck('user')
            ->filter();

        if ($primaryUsers->isNotEmpty()) {
            return $primaryUsers;
        }

        return $company->companyUsers()
            ->with('user')
            ->where('status', 'active')
            ->where('role', 'admin')
            ->get()
            ->pluck('user')
            ->filter();
    }

    protected function ensurePending(CondominiumLinkRequest $request): void
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages([
                'request' => 'Esta solicitação já foi resolvida.',
            ]);
        }
    }

    protected function requestSnapshot(CondominiumLinkRequest $request): array
    {
        $request->loadMissing(['condominium', 'requestingCompany', 'currentPrimaryCompany']);

        return [
            'request_id' => $request->id,
            'condominium_id' => $request->condominium_id,
            'condominium_name' => $request->condominium?->name,
            'requesting_company_id' => $request->requesting_company_id,
            'requesting_company_name' => $request->requestingCompany?->name,
            'current_primary_company_id' => $request->current_primary_company_id,
            'current_primary_company_name' => $request->currentPrimaryCompany?->name,
            'status' => $request->status,
            'decision_type' => $request->decision_type,
            'request_notes' => $request->request_notes,
            'decision_notes' => $request->decision_notes,
        ];
    }

    protected function ensureTargetCompanyCapacity(CondominiumLinkRequest $request): void
    {
        if ($request->condominium?->status !== 'active') {
            return;
        }

        if ($this->licenseGuard->canCreateCondominium($request->requestingCompany)) {
            return;
        }

        throw ValidationException::withMessages([
            'request' => 'A empresa solicitante não possui limite contratual disponível para receber este condomínio ativo.',
        ]);
    }
}
