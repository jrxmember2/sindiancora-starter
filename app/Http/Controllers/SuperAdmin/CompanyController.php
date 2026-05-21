<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        Company::create($data);

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa criada com sucesso.');
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('SuperAdmin/Companies/Form', ['company' => $company]);
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $company->update($this->validated($request, $company));

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa atualizada.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->update(['status' => 'inactive']);

        return back()->with('success', 'Empresa inativada.');
    }

    private function validated(Request $request, ?Company $company = null): array
    {
        $id = $company?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'responsible_name' => ['nullable', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:companies,slug,'.$id],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);
    }
}
