<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public function __construct(protected TenantResolver $tenantResolver)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->tenantResolver->bindCurrentCompany($request);

        return $next($request);
    }
}
