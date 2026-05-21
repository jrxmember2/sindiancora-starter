<?php

namespace App\Http\Middleware;

use App\Services\Licensing\LicenseGuard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleIsEnabled
{
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $company = app('currentCompany');

        if (! app(LicenseGuard::class)->canAccessModule($company, $moduleKey)) {
            return redirect()->route('dashboard')->with('error', "O módulo {$moduleKey} não está habilitado nesta licença.");
        }

        return $next($request);
    }
}
