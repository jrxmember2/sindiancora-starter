<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Document;
use App\Models\Issue;
use App\Services\Licensing\LicenseGuard;
use App\Services\Tenancy\TenantResolver;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        $user = auth()->user();
        $tenantResolver = app(TenantResolver::class);
        $issuesQuery = $company ? $tenantResolver->scopeByAccessibleCondominiums(Issue::query(), $user, $company) : null;
        $documentsQuery = $company ? $tenantResolver->scopeByAccessibleCondominiums(Document::query(), $user, $company, includeNull: true) : null;

        return Inertia::render('Dashboard', [
            'stats' => [
                'companies' => $user?->isSuperAdmin() ? Company::query()->count() : null,
                'issues_open' => $company
                    ? (clone $issuesQuery)->whereNotIn('status', ['finalizado', 'cancelado'])->count()
                    : 0,
                'issues_late' => $company
                    ? (clone $issuesQuery)
                        ->whereNotIn('status', ['finalizado', 'cancelado'])
                        ->whereNotNull('deadline_at')
                        ->where('deadline_at', '<', now())
                        ->count()
                    : 0,
                'documents_due' => $company
                    ? (clone $documentsQuery)
                        ->whereNotNull('valid_until')
                        ->whereBetween('valid_until', [now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()])
                        ->count()
                    : 0,
                'license_usage' => $company ? app(LicenseGuard::class)->usage($company) : null,
            ],
        ]);
    }
}
