<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CompanyUserStoreRequest;
use App\Http\Requests\Tenant\CompanyUserUpdateRequest;
use App\Models\CompanyUser;
use App\Services\Permissions\CompanyPermissionService;
use App\Services\Users\CompanyUserManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected CompanyUserManager $companyUserManager,
        protected CompanyPermissionService $permissionService,
    ) {
    }

    public function index(): Response
    {
        $this->authorize('viewAny', CompanyUser::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Users/Index', [
            'items' => $company->companyUsers()
                ->with(['user:id,name,email,phone,status', 'condominiums:id,name'])
                ->latest()
                ->paginate(15)
                ->through(fn (CompanyUser $companyUser) => $this->presentForIndex($companyUser)),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CompanyUser::class);

        return Inertia::render('Tenant/Users/Form', [
            'item' => null,
            'roleOptions' => $this->permissionService->roleOptions(),
            'condominiums' => $this->condominiumOptions(),
        ]);
    }

    public function store(CompanyUserStoreRequest $request): RedirectResponse
    {
        $this->companyUserManager->create($request->validated(), app('currentCompany'), $request->user());

        return redirect()->route('users.index')->with('success', 'Usuário interno salvo com sucesso.');
    }

    public function edit(CompanyUser $companyUser): Response
    {
        $this->authorize('update', $companyUser);

        $companyUser->load(['user', 'condominiums:id,name']);

        return Inertia::render('Tenant/Users/Form', [
            'item' => $this->presentForForm($companyUser),
            'roleOptions' => $this->permissionService->roleOptions(),
            'condominiums' => $this->condominiumOptions(),
        ]);
    }

    public function update(CompanyUserUpdateRequest $request, CompanyUser $companyUser): RedirectResponse
    {
        $this->companyUserManager->update($companyUser, $request->validated(), $request->user());

        return redirect()->route('users.index')->with('success', 'Usuário interno atualizado com sucesso.');
    }

    public function destroy(Request $request, CompanyUser $companyUser): RedirectResponse
    {
        $this->authorize('delete', $companyUser);

        try {
            $this->companyUserManager->deactivate($companyUser, $request->user());
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return back()->with('success', 'Usuário interno inativado.');
    }

    protected function condominiumOptions(): array
    {
        return app('currentCompany')->condominiums()
            ->where('condominiums.status', 'active')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($condominium) => [
                'id' => $condominium->id,
                'name' => $condominium->name,
            ])
            ->all();
    }

    protected function presentForIndex(CompanyUser $companyUser): array
    {
        $companyUser->loadMissing(['user', 'condominiums:id,name']);

        return [
            'id' => $companyUser->id,
            'name' => $companyUser->user?->name,
            'email' => $companyUser->user?->email,
            'phone' => $companyUser->user?->phone,
            'role' => $companyUser->role,
            'role_label' => $this->permissionService->roleLabel($companyUser->role),
            'status' => $companyUser->status,
            'can_access_whatsapp' => (bool) $companyUser->can_access_whatsapp,
            'only_responsible_issues' => (bool) $companyUser->only_responsible_issues,
            'condominiums' => $companyUser->condominiums
                ->sortBy('name')
                ->values()
                ->map(fn ($condominium) => ['id' => $condominium->id, 'name' => $condominium->name])
                ->all(),
            'has_full_condominium_access' => $companyUser->condominiums->isEmpty(),
        ];
    }

    protected function presentForForm(CompanyUser $companyUser): array
    {
        $companyUser->loadMissing(['user', 'condominiums:id,name']);

        return [
            'id' => $companyUser->id,
            'name' => $companyUser->user?->name,
            'email' => $companyUser->user?->email,
            'phone' => $companyUser->user?->phone,
            'role' => $companyUser->role,
            'status' => $companyUser->status,
            'can_access_whatsapp' => (bool) $companyUser->can_access_whatsapp,
            'only_responsible_issues' => (bool) $companyUser->only_responsible_issues,
            'condominium_ids' => $companyUser->condominiums->pluck('id')->values()->all(),
        ];
    }
}
