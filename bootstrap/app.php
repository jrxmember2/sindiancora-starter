<?php

use App\Http\Middleware\EnsureCompanySelected;
use App\Http\Middleware\EnsureLicenseIsActive;
use App\Http\Middleware\EnsureModuleIsEnabled;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetCurrentCompany;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            SetCurrentCompany::class,
        ]);

        $middleware->alias([
            'superadmin' => EnsureSuperAdmin::class,
            'company.selected' => EnsureCompanySelected::class,
            'license.active' => EnsureLicenseIsActive::class,
            'module' => EnsureModuleIsEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
