<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanySwitchRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;

class CompanySwitchController extends Controller
{
    public function __invoke(CompanySwitchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $allowed = $user->isSuperAdmin()
            ? Company::query()->whereKey($data['company_id'])->exists()
            : $user->companies()->where('companies.id', $data['company_id'])->exists();

        abort_unless($allowed, 403);

        session(['current_company_id' => $data['company_id']]);

        return back()->with('success', 'Empresa ativa alterada.');
    }
}
