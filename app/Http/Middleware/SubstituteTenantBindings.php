<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantResolver;
use Closure;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Middleware\SubstituteBindings;

class SubstituteTenantBindings extends SubstituteBindings
{
    public function __construct(Registrar $router, protected TenantResolver $tenantResolver)
    {
        parent::__construct($router);
    }

    public function handle($request, Closure $next)
    {
        $this->tenantResolver->bindCurrentCompany($request);

        return parent::handle($request, function ($request) use ($next) {
            $this->tenantResolver->bindCurrentCompany($request);

            return $next($request);
        });
    }
}
