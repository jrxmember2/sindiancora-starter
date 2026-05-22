<?php

use App\Http\Middleware\EnsureCompanySelected;
use App\Http\Middleware\EnsureLicenseIsActive;
use App\Http\Middleware\EnsurePasswordChangeIsComplete;
use App\Http\Middleware\EnsureModuleIsEnabled;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SubstituteTenantBindings;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_PREFIX |
            Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->web(
            append: [
                HandleInertiaRequests::class,
            ],
            replace: [
                SubstituteBindings::class => SubstituteTenantBindings::class,
            ],
        );

        $middleware->alias([
            'superadmin' => EnsureSuperAdmin::class,
            'company.selected' => EnsureCompanySelected::class,
            'license.active' => EnsureLicenseIsActive::class,
            'module' => EnsureModuleIsEnabled::class,
            'password.changed' => EnsurePasswordChangeIsComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
