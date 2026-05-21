<?php

namespace App\Services\Licensing;

use App\Models\Company;

class LicenseGuard
{
    public function isActive(Company $company): bool
    {
        $license = $company->activeLicense()->first();

        if (! $license) {
            return false;
        }

        if (! in_array($license->status, ['active', 'trial'], true)) {
            return false;
        }

        if ($license->ends_at && $license->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function canAccessModule(Company $company, string $moduleKey): bool
    {
        if (! $this->isActive($company)) {
            return false;
        }

        $license = $company->activeLicense()->with('modules')->first();

        return $license?->modules()
            ->where('modules.key', $moduleKey)
            ->wherePivot('enabled', true)
            ->exists() ?? false;
    }

    public function canCreateCondominium(Company $company): bool
    {
        $license = $company->activeLicense()->first();

        if (! $license) {
            return false;
        }

        $used = $company->condominiums()->withoutGlobalScopes()->where('status', 'active')->count();

        if ($used < $license->max_condominiums) {
            return true;
        }

        return (bool) $license->allow_overage && ! $license->block_new_records_on_limit;
    }

    public function canCreateInternalUser(Company $company): bool
    {
        $license = $company->activeLicense()->first();

        if (! $license) {
            return false;
        }

        $used = $company->companyUsers()->where('status', 'active')->count();

        if ($used < $license->max_internal_users) {
            return true;
        }

        return (bool) $license->allow_overage && ! $license->block_new_records_on_limit;
    }

    public function usage(Company $company): array
    {
        $license = $company->activeLicense()->first();

        return [
            'condominiums' => [
                'used' => $company->condominiums()->withoutGlobalScopes()->where('status', 'active')->count(),
                'limit' => $license?->max_condominiums ?? 0,
            ],
            'internal_users' => [
                'used' => $company->companyUsers()->where('status', 'active')->count(),
                'limit' => $license?->max_internal_users ?? 0,
            ],
        ];
    }
}
