<?php

namespace App\Services\Licensing;

use App\Models\Company;
use App\Models\License;
use App\Models\LicenseUsage;

class LicenseUsageService
{
    public function sync(Company|License $target): LicenseUsage
    {
        $company = $target instanceof Company ? $target : $target->company()->firstOrFail();
        $trackedLicense = $target instanceof License ? $target : $this->trackedLicense($company);
        $existing = $company->licenseUsage()->first();

        return LicenseUsage::query()->updateOrCreate(
            ['company_id' => $company->id],
            [
                'license_id' => $trackedLicense?->id,
                'active_condominiums' => $company->condominiums()->withoutGlobalScopes()->where('status', 'active')->count(),
                'active_internal_users' => $company->companyUsers()->where('status', 'active')->count(),
                'storage_used_mb' => $existing?->storage_used_mb ?? 0,
                'whatsapp_instances_used' => $existing?->whatsapp_instances_used ?? 0,
                'ai_credits_used_month' => $existing?->ai_credits_used_month ?? 0,
            ]
        );
    }

    public function trackedLicense(Company $company): ?License
    {
        return $company->activeLicense()->first()
            ?? $company->licenses()->latest('starts_at')->latest('id')->first();
    }
}
