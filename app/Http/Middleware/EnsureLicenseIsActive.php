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
        $status = app(LicenseGuard::class)->status($company);

        if (! $status['allows_access']) {
            return redirect()->route('dashboard')->with('error', $status['message']);
        }

        if (! $status['allows_write'] && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return redirect()->route('dashboard')->with('error', $status['message']);
        }

        return $next($request);
    }
}
