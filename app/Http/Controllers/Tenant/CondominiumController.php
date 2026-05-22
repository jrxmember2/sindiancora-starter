<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CondominiumRequest;
use App\Models\Condominium;
use App\Services\Condominiums\CondominiumManager;
use App\Services\Licensing\LicenseGuard;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumController extends Controller
{
    public function __construct(
        protected CondominiumManager $condominiumManager,
        protected TenantResolver $tenantResolver,
        protected LicenseGuard $licenseGuard,
    ) {
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Condominium::class);

        $company = app('currentCompany');
        $query = $this->tenantResolver->accessibleCondominiumsQuery($request->user(), $company)
            ->with([
                'companyLinks' => fn ($linkQuery) => $linkQuery
                    ->where('company_id', $company->id)
                    ->where('status', 'active'),
            ])
            ->orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderBy('name');

        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => trim((string) $request->string('status')),
        ];

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($inner) use ($search) {
                $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('administrator_name', 'like', "%{$search}%");
            });
        }

        if (in_array($filters['status'], ['active', 'inactive'], true)) {
            $query->where('status', $filters['status']);
        }

        $usage = $this->licenseGuard->usage($company);

        return Inertia::render('Tenant/Condominiums/Index', [
            'items' => $query
                ->paginate(15)
                ->withQueryString()
                ->through(fn (Condominium $condominium) => $this->presentForIndex($condominium)),
            'filters' => $filters,
            'summary' => [
                'active' => $usage['condominiums']['used'],
                'inactive' => $company->condominiums()
                    ->where('condominiums.status', 'inactive')
                    ->distinct('condominiums.id')
                    ->count('condominiums.id'),
                'limit' => $usage['condominiums']['limit'],
                'remaining' => $usage['condominiums']['remaining'],
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Condominium::class);

        return Inertia::render('Tenant/Condominiums/Form', ['item' => null]);
    }

    public function store(CondominiumRequest $request): RedirectResponse
    {
        $this->authorize('create', Condominium::class);

        $result = $this->condominiumManager->createOrRequestAccess(
            $request->validated(),
            app('currentCompany'),
            $request->user()
        );

        if ($result['status'] === 'requested') {
            return redirect()
                ->route('tenant.condominium-links.index')
                ->with('success', 'O CNPJ informado já existe na plataforma. Uma solicitação de vínculo foi aberta para análise da empresa administradora atual.');
        }

        return redirect()->route('condominiums.index')->with('success', 'Condomínio criado com sucesso.');
    }

    public function edit(Condominium $condominium): Response
    {
        $this->authorize('update', $condominium);

        abort_unless(
            $this->tenantResolver->canAccessCondominium(request()->user(), app('currentCompany'), $condominium->id),
            404
        );

        return Inertia::render('Tenant/Condominiums/Form', [
            'item' => $this->presentForForm($condominium),
        ]);
    }

    public function update(CondominiumRequest $request, Condominium $condominium): RedirectResponse
    {
        $this->authorize('update', $condominium);

        abort_unless(
            $this->tenantResolver->canAccessCondominium($request->user(), app('currentCompany'), $condominium->id),
            404
        );

        $this->condominiumManager->update($condominium, $request->validated(), $request->user());

        return redirect()->route('condominiums.index')->with('success', 'Condomínio atualizado com sucesso.');
    }

    public function destroy(Condominium $condominium): RedirectResponse
    {
        $this->authorize('delete', $condominium);

        abort_unless(
            $this->tenantResolver->canAccessCondominium(request()->user(), app('currentCompany'), $condominium->id),
            404
        );

        $this->condominiumManager->inactivate($condominium, request()->user());

        return back()->with('success', 'Condomínio inativado com sucesso.');
    }

    protected function presentForIndex(Condominium $condominium): array
    {
        $link = $condominium->companyLinks->first();

        return [
            'id' => $condominium->id,
            'name' => $condominium->name,
            'document' => $condominium->document,
            'email' => $condominium->email,
            'phone' => $condominium->phone,
            'city' => $condominium->city,
            'state' => $condominium->state,
            'status' => $condominium->status,
            'status_label' => $condominium->status === 'active' ? 'Ativo' : 'Inativo',
            'administrator_name' => $condominium->administrator_name,
            'mandate_start' => $condominium->mandate_start?->format('d/m/Y'),
            'mandate_end' => $condominium->mandate_end?->format('d/m/Y'),
            'logo_url' => $condominium->logo_url,
            'relationship_type' => $link?->relationship_type,
            'relationship_label' => match ($link?->relationship_type) {
                'principal' => 'Principal',
                'solidaria' => 'Solidária',
                default => 'Vínculo ativo',
            },
            'can_manage_registry' => $link?->relationship_type === 'principal',
            'initials' => collect(explode(' ', $condominium->name))
                ->filter()
                ->take(2)
                ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
                ->join(''),
        ];
    }

    protected function presentForForm(Condominium $condominium): array
    {
        return [
            'id' => $condominium->id,
            'name' => $condominium->name,
            'document' => $condominium->document,
            'email' => $condominium->email,
            'phone' => $condominium->phone,
            'slug' => $condominium->slug,
            'status' => $condominium->status,
            'cep' => $condominium->cep,
            'street' => $condominium->street,
            'number' => $condominium->number,
            'complement' => $condominium->complement,
            'district' => $condominium->district,
            'city' => $condominium->city,
            'state' => $condominium->state,
            'mandate_start' => $condominium->mandate_start?->toDateString(),
            'mandate_end' => $condominium->mandate_end?->toDateString(),
            'administrator_name' => $condominium->administrator_name,
            'logo_url' => $condominium->logo_url,
        ];
    }
}
