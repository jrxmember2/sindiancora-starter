<?php

namespace App\Services\Users;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompanyUserManager
{
    public function __construct(protected AuditLogger $auditLogger)
    {
    }

    public function create(array $data, Company $company, ?User $actor = null): CompanyUser
    {
        return DB::transaction(function () use ($data, $company, $actor) {
            $user = User::query()->where('email', $data['email'])->first();

            if ($user) {
                if ($user->isSuperAdmin()) {
                    throw ValidationException::withMessages([
                        'email' => 'Este e-mail já pertence a um usuário da plataforma e não pode ser reutilizado como usuário interno do tenant.',
                    ]);
                }

                $this->fillUserProfile($user, $data);
            } else {
                $user = User::query()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $data['password'],
                    'status' => $data['status'],
                ]);
            }

            $companyUser = CompanyUser::query()->create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'role' => $data['role'],
                'status' => $data['status'],
                'can_access_whatsapp' => $data['can_access_whatsapp'] ?? false,
                'only_responsible_issues' => $data['only_responsible_issues'] ?? false,
            ]);

            $companyUser->condominiums()->sync($data['condominium_ids'] ?? []);
            $this->syncUserStatus($user);

            $companyUser = $companyUser->load(['user', 'condominiums:id,name']);

            $this->auditLogger->record(
                action: 'company_user.created',
                actor: $actor,
                auditable: $companyUser,
                company: $company,
                newValues: $this->snapshot($companyUser),
            );

            return $companyUser;
        });
    }

    public function update(CompanyUser $companyUser, array $data, User $actor): CompanyUser
    {
        return DB::transaction(function () use ($companyUser, $data, $actor) {
            $before = $this->snapshot($companyUser->loadMissing(['user', 'condominiums:id,name']));

            $this->guardPrimaryAdminMembership($companyUser, $data['role'], $data['status']);
            $this->guardAgainstRemovingLastActiveAdmin($companyUser, $data['role'], $data['status']);

            if ($companyUser->user_id === $actor->id && ($data['status'] !== 'active')) {
                throw ValidationException::withMessages([
                    'status' => 'Você não pode inativar o seu próprio vínculo nesta tela.',
                ]);
            }

            $this->fillUserProfile($companyUser->user, $data);

            if (! empty($data['password'])) {
                $companyUser->user->update(['password' => $data['password']]);
            }

            $companyUser->update([
                'role' => $data['role'],
                'status' => $data['status'],
                'can_access_whatsapp' => $data['can_access_whatsapp'] ?? false,
                'only_responsible_issues' => $data['only_responsible_issues'] ?? false,
            ]);

            $companyUser->condominiums()->sync($data['condominium_ids'] ?? []);
            $this->syncUserStatus($companyUser->user);

            $companyUser = $companyUser->refresh()->load(['user', 'condominiums:id,name']);

            $this->auditLogger->record(
                action: 'company_user.updated',
                actor: $actor,
                auditable: $companyUser,
                company: $companyUser->company,
                oldValues: $before,
                newValues: $this->snapshot($companyUser),
            );

            return $companyUser;
        });
    }

    public function deactivate(CompanyUser $companyUser, User $actor): void
    {
        DB::transaction(function () use ($companyUser, $actor) {
            $before = $this->snapshot($companyUser->loadMissing(['user', 'condominiums:id,name']));

            $this->guardPrimaryAdminMembership($companyUser, $companyUser->role, 'inactive');
            $this->guardAgainstRemovingLastActiveAdmin($companyUser, $companyUser->role, 'inactive');

            if ($companyUser->user_id === $actor->id) {
                throw ValidationException::withMessages([
                    'company_user' => 'Você não pode inativar o seu próprio vínculo nesta tela.',
                ]);
            }

            $companyUser->update(['status' => 'inactive']);
            $this->syncUserStatus($companyUser->user);

            $companyUser = $companyUser->refresh()->load(['user', 'condominiums:id,name']);

            $this->auditLogger->record(
                action: 'company_user.deactivated',
                actor: $actor,
                auditable: $companyUser,
                company: $companyUser->company,
                oldValues: $before,
                newValues: $this->snapshot($companyUser),
            );
        });
    }

    public function syncUserStatus(User $user): void
    {
        if ($user->isSuperAdmin()) {
            return;
        }

        $user->update([
            'status' => $user->companyUsers()->where('status', 'active')->exists() ? 'active' : 'inactive',
        ]);
    }

    protected function fillUserProfile(User $user, array $data): void
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
    }

    protected function guardAgainstRemovingLastActiveAdmin(CompanyUser $companyUser, string $nextRole, string $nextStatus): void
    {
        $isRemovingAdminCoverage = $companyUser->role === 'admin'
            && $companyUser->status === 'active'
            && ($nextRole !== 'admin' || $nextStatus !== 'active');

        if (! $isRemovingAdminCoverage) {
            return;
        }

        $hasAnotherAdmin = CompanyUser::query()
            ->where('company_id', $companyUser->company_id)
            ->where('id', '!=', $companyUser->id)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->exists();

        if (! $hasAnotherAdmin) {
            throw ValidationException::withMessages([
                'role' => 'A empresa precisa manter pelo menos um admin ativo.',
            ]);
        }
    }

    protected function guardPrimaryAdminMembership(CompanyUser $companyUser, string $nextRole, string $nextStatus): void
    {
        if (! $companyUser->is_primary) {
            return;
        }

        if ($nextRole === 'admin' && $nextStatus === 'active') {
            return;
        }

        throw ValidationException::withMessages([
            'role' => 'O admin master da empresa deve ser mantido pelo superadmin na área comercial da plataforma.',
        ]);
    }

    protected function snapshot(CompanyUser $companyUser): array
    {
        $companyUser->loadMissing(['user', 'condominiums:id,name']);

        return [
            'company_user_id' => $companyUser->id,
            'company_id' => $companyUser->company_id,
            'user_id' => $companyUser->user_id,
            'name' => $companyUser->user?->name,
            'email' => $companyUser->user?->email,
            'phone' => $companyUser->user?->phone,
            'role' => $companyUser->role,
            'status' => $companyUser->status,
            'can_access_whatsapp' => (bool) $companyUser->can_access_whatsapp,
            'only_responsible_issues' => (bool) $companyUser->only_responsible_issues,
            'condominium_ids' => $companyUser->condominiums->pluck('id')->values()->all(),
        ];
    }
}
