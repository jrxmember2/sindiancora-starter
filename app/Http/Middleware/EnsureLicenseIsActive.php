<?php

namespace App\Http\Middleware;

use App\Services\Licensing\LicenseGuard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLicenseIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = app('currentCompany');

        if (! $company || ! app(LicenseGuard::class)->isActive($company)) {
            return redirect()->route('dashboard')->with('error', 'A licenca desta empresa nao esta ativa.');
        }

        return $next($request);
    }
}
