<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->bound('currentCompany') || ! app('currentCompany')) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma empresa para continuar.');
        }

        return $next($request);
    }
}
