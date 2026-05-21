<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Suppliers/Index', [
            'items' => Supplier::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Suppliers/Form', ['item' => null]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor criado com sucesso.');
    }

    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('Tenant/Suppliers/Form', ['item' => $supplier]);
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['active' => false]);

        return back()->with('success', 'Fornecedor inativado.');
    }
}
