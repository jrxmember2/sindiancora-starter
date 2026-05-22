<?php

namespace App\Services\Permissions;

use App\Models\Company;
use App\Models\Condominium;
use App\Models\User;
use App\Services\Tenancy\TenantResolver;

class CompanyPermissionService
{
    public function __construct(protected TenantResolver $tenantResolver)
    {
    }

    public function abilities(?User $user, ?Company $company): array
    {
        $abilityKeys = [
            'view_company_users',
            'create_company_users',
            'update_company_users',
            'deactivate_company_users',
            'assign_user_condominiums',
            'view_condominiums',
            'create_condominiums',
            'update_condominiums',
            'deactivate_condominiums',
        ];

        $abilities = array_fill_keys($abilityKeys, false);

        if (! $user) {
            return $abilities;
        }

        if ($user->isSuperAdmin()) {
            return array_fill_keys($abilityKeys, true);
        }

        if (! $company) {
            return $abilities;
        }

        $membership = $this->tenantResolver->currentCompanyUser($user, $company);

        if (! $membership) {
            return $abilities;
        }

        foreach ($this->roleAbilities($membership->role) as $ability) {
            $abilities[$ability] = true;
        }

        return $abilities;
    }

    public function can(?User $user, ?Company $company, string $ability, mixed $target = null): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if (! $company) {
            return false;
        }

        $abilities = $this->abilities($user, $company);

        if (! ($abilities[$ability] ?? false)) {
            return false;
        }

        if ($target instanceof Condominium) {
            return $target->companyLinks()
                ->where('company_id', $company->id)
                ->where('status', 'active')
                ->exists();
        }

        if ($target && isset($target->company_id) && (int) $target->company_id !== (int) $company->id) {
            return false;
        }

        return true;
    }

    public function roleOptions(): array
    {
        return collect(config('company_permissions.roles', []))
            ->map(fn (array $role, string $key) => [
                'value' => $key,
                'label' => $role['label'],
                'description' => $role['description'],
            ])
            ->values()
            ->all();
    }

    public function roleLabel(?string $role): string
    {
        return config("company_permissions.roles.{$role}.label", ucfirst((string) $role));
    }

    protected function roleAbilities(?string $role): array
    {
        return config("company_permissions.roles.{$role}.abilities", []);
    }
}
