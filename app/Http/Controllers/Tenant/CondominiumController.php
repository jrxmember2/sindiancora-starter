<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CondominiumRequest;
use App\Models\Condominium;
use App\Services\Licensing\LicenseGuard;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumController extends Controller
{
    public function index(): Response
    {
        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Condominiums/Index', [
            'items' => $tenantResolver
                ->accessibleCondominiumsQuery(request()->user(), $company)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Condominiums/Form', ['item' => null]);
    }

    public function store(CondominiumRequest $request): RedirectResponse
    {
        abort_unless(
            app(LicenseGuard::class)->canCreateCondominium(app('currentCompany')),
            403,
            'Limite de condominios da licenca atingido.'
        );

        Condominium::create($request->validated());

        return redirect()->route('condominiums.index')->with('success', 'Condominio criado com sucesso.');
    }

    public function edit(Condominium $condominium): Response
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium(request()->user(), app('currentCompany'), $condominium->id),
            404
        );

        return Inertia::render('Tenant/Condominiums/Form', ['item' => $condominium]);
    }

    public function update(CondominiumRequest $request, Condominium $condominium): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium($request->user(), app('currentCompany'), $condominium->id),
            404
        );

        $condominium->update($request->validated());

        return redirect()->route('condominiums.index')->with('success', 'Condominio atualizado.');
    }

    public function destroy(Condominium $condominium): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium(request()->user(), app('currentCompany'), $condominium->id),
            404
        );

        $condominium->update(['status' => 'inactive']);

        return back()->with('success', 'Condominio inativado.');
    }
}
