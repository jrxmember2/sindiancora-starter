<?php

namespace App\Http\Middleware;

use App\Services\Licensing\LicenseGuard;
use App\Services\Permissions\CompanyPermissionService;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(protected TenantResolver $tenantResolver)
    {
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        $licenseGuard = app(LicenseGuard::class);
        $permissionService = app(CompanyPermissionService::class);
        $membership = ($user && $company) ? $this->tenantResolver->currentCompanyUser($user, $company) : null;

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_superadmin' => (bool) $user->is_superadmin,
                    'must_change_password' => (bool) $user->must_change_password,
                ] : null,
                'notifications' => $user ? [
                    'unread_count' => $user->unreadNotifications()->count(),
                ] : [
                    'unread_count' => 0,
                ],
            ],
            'tenant' => [
                'currentCompany' => $company ? [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'status' => $company->status,
                ] : null,
                'currentMembership' => $membership ? [
                    'id' => $membership->id,
                    'role' => $membership->role,
                    'status' => $membership->status,
                    'is_primary' => (bool) $membership->is_primary,
                    'can_access_whatsapp' => (bool) $membership->can_access_whatsapp,
                    'only_responsible_issues' => (bool) $membership->only_responsible_issues,
                ] : null,
                'companies' => $user ? $this->tenantResolver->companiesForUser($user) : [],
                'licenseStatus' => $company ? $licenseGuard->status($company) : null,
                'licenseAlerts' => $company ? $licenseGuard->alerts($company) : [],
                'licenseUsage' => $company ? $licenseGuard->usage($company) : null,
                'moduleAccess' => $company ? $licenseGuard->moduleAccessMap($company) : [],
                'abilities' => ($user && $company) ? $permissionService->abilities($user, $company) : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
