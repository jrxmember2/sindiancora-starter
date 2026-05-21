<?php

namespace App\Http\Middleware;

use App\Services\Licensing\LicenseGuard;
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

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_superadmin' => (bool) $user->is_superadmin,
                ] : null,
            ],
            'tenant' => [
                'currentCompany' => $company ? [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'status' => $company->status,
                ] : null,
                'companies' => $user ? $this->tenantResolver->companiesForUser($user) : [],
                'licenseUsage' => $company ? app(LicenseGuard::class)->usage($company) : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
