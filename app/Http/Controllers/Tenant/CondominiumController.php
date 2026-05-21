<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Condominium;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Generic/Index', [
            'title' => 'Condomínios',
            'description' => 'Gerencie os condomínios ativos e inativos da empresa.',
            'items' => Condominium::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Novo condomínio',
            'item' => null,
            'fields' => ['name', 'document', 'email', 'phone', 'status'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(app(LicenseGuard::class)->canCreateCondominium(app('currentCompany')), 403, 'Limite de condomínios da licença atingido.');

        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Condominium::create($data);

        return redirect()->route('condominiums.index')->with('success', 'Condomínio criado com sucesso.');
    }

    public function edit(Condominium $condominium): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Editar condomínio',
            'item' => $condominium,
            'fields' => ['name', 'document', 'email', 'phone', 'status'],
        ]);
    }

    public function update(Request $request, Condominium $condominium): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? $condominium->slug ?? Str::slug($data['name']);

        $condominium->update($data);

        return redirect()->route('condominiums.index')->with('success', 'Condomínio atualizado.');
    }

    public function destroy(Condominium $condominium): RedirectResponse
    {
        $condominium->update(['status' => 'inactive']);

        return back()->with('success', 'Condomínio inativado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'slug' => ['nullable', 'string', 'max:160'],
            'status' => ['required', 'in:active,inactive'],
            'cep' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:180'],
            'number' => ['nullable', 'string', 'max:40'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:2'],
            'mandate_start' => ['nullable', 'date'],
            'mandate_end' => ['nullable', 'date'],
            'administrator_name' => ['nullable', 'string', 'max:180'],
        ]);
    }
}
