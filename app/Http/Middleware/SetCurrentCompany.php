<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $company = null;

        if ($user) {
            $companyId = session('current_company_id');

            if ($companyId) {
                $company = $user->isSuperAdmin()
                    ? Company::query()->withoutGlobalScopes()->find($companyId)
                    : $user->companies()->where('companies.id', $companyId)->first();
            }

            if (! $company && ! $user->isSuperAdmin()) {
                $company = $user->companies()->wherePivot('status', 'active')->first();

                if ($company) {
                    session(['current_company_id' => $company->id]);
                }
            }
        }

        app()->instance('currentCompany', $company);

        return $next($request);
    }
}
