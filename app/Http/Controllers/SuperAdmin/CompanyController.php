<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\CompanyRequest;
use App\Models\Company;
use App\Services\Companies\CompanyOnboardingManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function __construct(protected CompanyOnboardingManager $companyOnboardingManager)
    {
    }

    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Companies/Index', [
            'companies' => Company::query()
                ->with('primaryCompanyUser.user:id,name,email')
                ->latest()
                ->paginate(15),
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

        $this->companyOnboardingManager->create($data, $request->user());

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa e admin master inicial criados com sucesso.');
    }

    public function edit(Company $company): Response
    {
        $company->load('primaryCompanyUser.user');

        return Inertia::render('SuperAdmin/Companies/Form', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'document' => $company->document,
                'email' => $company->email,
                'phone' => $company->phone,
                'responsible_name' => $company->responsible_name,
                'slug' => $company->slug,
                'primary_color' => $company->primary_color,
                'secondary_color' => $company->secondary_color,
                'status' => $company->status,
                'primary_user_name' => $company->primaryCompanyUser?->user?->name,
                'primary_user_email' => $company->primaryCompanyUser?->user?->email,
                'primary_user_phone' => $company->primaryCompanyUser?->user?->phone,
                'primary_user_force_password_reset' => (bool) $company->primaryCompanyUser?->user?->must_change_password,
            ],
        ]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $this->companyOnboardingManager->update($company, $data, $request->user());

        return redirect()->route('superadmin.companies.index')->with('success', 'Empresa e admin master atualizados com sucesso.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->update(['status' => 'inactive']);

        return back()->with('success', 'Empresa inativada.');
    }
}
