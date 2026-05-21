<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Generic/Index', [
            'title' => 'Fornecedores',
            'description' => 'Cadastro central de fornecedores da empresa.',
            'items' => Supplier::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Novo fornecedor',
            'item' => null,
            'fields' => ['name', 'document', 'email', 'phone', 'status'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor criado com sucesso.');
    }

    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Editar fornecedor',
            'item' => $supplier,
            'fields' => ['name', 'document', 'email', 'phone', 'status'],
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['active' => false]);

        return back()->with('success', 'Fornecedor inativado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'responsible_name' => ['nullable', 'string', 'max:160'],
            'mobile' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'string', 'max:180'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'cep' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:180'],
            'number' => ['nullable', 'string', 'max:40'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:2'],
            'country' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
            'active' => ['boolean'],
        ]);
    }
}
