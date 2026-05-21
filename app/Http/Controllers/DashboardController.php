<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Document;
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
                'issues_open' => $company
                    ? Issue::query()->whereNotIn('status', ['finalizado', 'cancelado'])->count()
                    : 0,
                'issues_late' => $company
                    ? Issue::query()
                        ->whereNotIn('status', ['finalizado', 'cancelado'])
                        ->whereNotNull('deadline_at')
                        ->where('deadline_at', '<', now())
                        ->count()
                    : 0,
                'documents_due' => $company
                    ? Document::query()
                        ->whereNotNull('valid_until')
                        ->whereBetween('valid_until', [now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()])
                        ->count()
                    : 0,
                'license_usage' => $company ? app(LicenseGuard::class)->usage($company) : null,
            ],
        ]);
    }
}
