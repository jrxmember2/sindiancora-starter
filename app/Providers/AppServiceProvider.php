<?php

namespace App\Providers;

use App\Models\CompanyUser;
use App\Models\User;
use App\Policies\CompanyUserPolicy;
use App\Services\Permissions\CompanyPermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->isProduction()) {
            URL::forceHttps();
        }

        Gate::policy(CompanyUser::class, CompanyUserPolicy::class);

        Gate::define('view-company-users', function (User $user) {
            return app(CompanyPermissionService::class)->can($user, $this->currentCompany(), 'view_company_users');
        });

        Gate::define('create-company-users', function (User $user) {
            return app(CompanyPermissionService::class)->can($user, $this->currentCompany(), 'create_company_users');
        });

        Gate::define('update-company-users', function (User $user, CompanyUser $companyUser) {
            return app(CompanyPermissionService::class)->can($user, $this->currentCompany(), 'update_company_users', $companyUser);
        });

        Gate::define('deactivate-company-users', function (User $user, CompanyUser $companyUser) {
            return app(CompanyPermissionService::class)->can($user, $this->currentCompany(), 'deactivate_company_users', $companyUser);
        });
    }

    protected function currentCompany(): mixed
    {
        return app()->bound('currentCompany') ? app('currentCompany') : null;
    }
}
