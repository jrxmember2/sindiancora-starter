<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate(['company_id' => ['required', 'integer']]);
        $user = $request->user();

        $allowed = $user->isSuperAdmin()
            ? Company::query()->whereKey($data['company_id'])->exists()
            : $user->companies()->where('companies.id', $data['company_id'])->exists();

        abort_unless($allowed, 403);

        session(['current_company_id' => $data['company_id']]);

        return back()->with('success', 'Empresa ativa alterada.');
    }
}
