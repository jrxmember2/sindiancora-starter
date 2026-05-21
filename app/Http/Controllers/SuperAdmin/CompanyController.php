<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Companies/Index', [
            'companies' => Company::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SuperAdmin/Companies/Form', ['company' => null]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        Company::create($data);

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa criada com sucesso.');
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('SuperAdmin/Companies/Form', ['company' => $company]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa atualizada.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->update(['status' => 'inactive']);

        return back()->with('success', 'Empresa inativada.');
    }
}
