<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanySwitchRequest;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;

class CompanySwitchController extends Controller
{
    public function __invoke(CompanySwitchRequest $request, TenantResolver $tenantResolver): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $allowed = $tenantResolver->canSwitchToCompany($user, (int) $data['company_id']);

        abort_unless($allowed, 403);

        session(['current_company_id' => $data['company_id']]);

        return back()->with('success', 'Empresa ativa alterada.');
    }
}
