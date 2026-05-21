<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CondominiumRequest;
use App\Models\Condominium;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Condominiums/Index', [
            'items' => Condominium::query()->latest()->paginate(15),
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
        return Inertia::render('Tenant/Condominiums/Form', ['item' => $condominium]);
    }

    public function update(CondominiumRequest $request, Condominium $condominium): RedirectResponse
    {
        $condominium->update($request->validated());

        return redirect()->route('condominiums.index')->with('success', 'Condominio atualizado.');
    }

    public function destroy(Condominium $condominium): RedirectResponse
    {
        $condominium->update(['status' => 'inactive']);

        return back()->with('success', 'Condominio inativado.');
    }
}
