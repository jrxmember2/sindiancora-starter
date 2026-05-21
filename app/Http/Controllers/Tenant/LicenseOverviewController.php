<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Licensing\LicenseGuard;
use Inertia\Inertia;
use Inertia\Response;

class LicenseOverviewController extends Controller
{
    public function __invoke(LicenseGuard $licenseGuard): Response
    {
        $company = app('currentCompany');
        $license = $licenseGuard->currentLicense($company);

        if ($company) {
            $licenseGuard->syncUsageSnapshot($company);
        }

        $license?->load('modules:id,name,key,category');

        return Inertia::render('Tenant/License/Show', [
            'license' => $license ? [
                'id' => $license->id,
                'contract_number' => $license->contract_number,
                'status' => $license->status,
                'financial_status' => $license->financial_status,
                'billing_type' => $license->billing_type,
                'monthly_amount' => $license->monthly_amount,
                'setup_amount' => $license->setup_amount,
                'billing_day' => $license->billing_day,
                'starts_at' => optional($license->starts_at)->toDateString(),
                'ends_at' => optional($license->ends_at)->toDateString(),
                'renews_at' => optional($license->renews_at)->toDateString(),
                'max_condominiums' => $license->max_condominiums,
                'max_internal_users' => $license->max_internal_users,
                'max_storage_mb' => $license->max_storage_mb,
                'max_whatsapp_instances' => $license->max_whatsapp_instances,
                'monthly_ai_credits' => $license->monthly_ai_credits,
                'allow_overage' => $license->allow_overage,
                'block_new_records_on_limit' => $license->block_new_records_on_limit,
                'read_only_when_expired' => $license->read_only_when_expired,
                'auto_suspend_when_overdue' => $license->auto_suspend_when_overdue,
                'notes' => $license->notes,
                'modules' => $license->modules
                    ->where('pivot.enabled', true)
                    ->sortBy(['category', 'name'])
                    ->values()
                    ->map(fn ($module) => [
                        'id' => $module->id,
                        'key' => $module->key,
                        'name' => $module->name,
                        'category' => $module->category,
                    ]),
            ] : null,
            'statusSummary' => $licenseGuard->status($company),
            'usage' => $licenseGuard->usage($company),
            'alerts' => $licenseGuard->alerts($company),
        ]);
    }
}
