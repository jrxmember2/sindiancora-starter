<?php

namespace App\Services\Companies;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyOnboardingManager
{
    public function __construct(protected AuditLogger $auditLogger)
    {
    }

    public function create(array $data, User $actor): Company
    {
        return DB::transaction(function () use ($data, $actor) {
            $company = Company::query()->create($this->companyPayload($data));
            $primaryUser = User::query()->create($this->primaryUserPayload($data));

            CompanyUser::query()->create([
                'company_id' => $company->id,
                'user_id' => $primaryUser->id,
                'role' => 'admin',
                'status' => 'active',
                'can_access_whatsapp' => false,
                'only_responsible_issues' => false,
                'is_primary' => true,
            ]);

            $company->load('primaryCompanyUser.user');

            $this->auditLogger->record(
                action: 'company.created',
                actor: $actor,
                auditable: $company,
                company: $company,
                newValues: $this->snapshot($company),
            );

            return $company;
        });
    }

    public function update(Company $company, array $data, User $actor): Company
    {
        return DB::transaction(function () use ($company, $data, $actor) {
            $company->load('primaryCompanyUser.user');
            $before = $this->snapshot($company);

            $company->update($this->companyPayload($data));

            $primaryMembership = $company->primaryCompanyUser()->with('user')->first();

            if ($primaryMembership) {
                $primaryMembership->update([
                    'role' => 'admin',
                    'status' => 'active',
                    'is_primary' => true,
                ]);

                $userPayload = Arr::except($this->primaryUserPayload($data), ['password']);

                $primaryMembership->user->update($userPayload);

                if (! empty($data['primary_user_password'])) {
                    $primaryMembership->user->update([
                        'password' => $data['primary_user_password'],
                        'must_change_password' => (bool) ($data['primary_user_force_password_reset'] ?? true),
                    ]);
                }
            } else {
                $primaryUser = User::query()->create($this->primaryUserPayload($data));

                CompanyUser::query()->create([
                    'company_id' => $company->id,
                    'user_id' => $primaryUser->id,
                    'role' => 'admin',
                    'status' => 'active',
                    'can_access_whatsapp' => false,
                    'only_responsible_issues' => false,
                    'is_primary' => true,
                ]);
            }

            $company->refresh()->load('primaryCompanyUser.user');

            $this->auditLogger->record(
                action: 'company.updated',
                actor: $actor,
                auditable: $company,
                company: $company,
                oldValues: $before,
                newValues: $this->snapshot($company),
            );

            return $company;
        });
    }

    protected function companyPayload(array $data): array
    {
        $payload = Arr::only($data, [
            'name',
            'document',
            'email',
            'phone',
            'responsible_name',
            'slug',
            'primary_color',
            'secondary_color',
            'status',
        ]);

        if (empty($payload['responsible_name']) && ! empty($data['primary_user_name'])) {
            $payload['responsible_name'] = $data['primary_user_name'];
        }

        return $payload;
    }

    protected function primaryUserPayload(array $data): array
    {
        return [
            'name' => $data['primary_user_name'],
            'email' => $data['primary_user_email'],
            'phone' => $data['primary_user_phone'] ?? null,
            'password' => $data['primary_user_password'],
            'status' => 'active',
            'is_superadmin' => false,
            'must_change_password' => (bool) ($data['primary_user_force_password_reset'] ?? true),
        ];
    }

    protected function snapshot(Company $company): array
    {
        $company->loadMissing('primaryCompanyUser.user');

        return [
            'company_id' => $company->id,
            'name' => $company->name,
            'document' => $company->document,
            'email' => $company->email,
            'phone' => $company->phone,
            'responsible_name' => $company->responsible_name,
            'slug' => $company->slug,
            'status' => $company->status,
            'primary_user' => [
                'company_user_id' => $company->primaryCompanyUser?->id,
                'user_id' => $company->primaryCompanyUser?->user_id,
                'name' => $company->primaryCompanyUser?->user?->name,
                'email' => $company->primaryCompanyUser?->user?->email,
                'phone' => $company->primaryCompanyUser?->user?->phone,
                'must_change_password' => (bool) $company->primaryCompanyUser?->user?->must_change_password,
            ],
        ];
    }
}
