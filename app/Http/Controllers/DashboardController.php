<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Issue;
use App\Services\Licensing\LicenseGuard;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;

        return Inertia::render('Dashboard', [
            'stats' => [
                'companies' => auth()->user()?->isSuperAdmin() ? Company::query()->count() : null,
                'issues_open' => $company ? Issue::query()->whereNotIn('status', ['finalizado', 'cancelado'])->count() : 0,
                'issues_late' => $company ? Issue::query()->whereNotIn('status', ['finalizado', 'cancelado'])->where('deadline_at', '<', now())->count() : 0,
                'license_usage' => $company ? app(LicenseGuard::class)->usage($company) : null,
            ],
        ]);
    }
}
