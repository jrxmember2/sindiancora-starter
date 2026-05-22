<?php

namespace App\Policies;

use App\Models\CompanyCondominium;
use App\Models\Condominium;
use App\Models\User;
use App\Services\Permissions\CompanyPermissionService;
use Illuminate\Auth\Access\Response;

class CondominiumPolicy
{
    public function __construct(protected CompanyPermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->can($user, $this->currentCompany(), 'view_condominiums');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->can($user, $this->currentCompany(), 'create_condominiums');
    }

    public function update(User $user, Condominium $condominium): Response
    {
        if ($user->isSuperAdmin()) {
            return $this->permissionService->can($user, $this->currentCompany(), 'update_condominiums', $condominium)
                ? Response::allow()
                : Response::deny('Você não tem permissão para editar este condomínio.');
        }

        $link = $this->currentCompanyLink($condominium);

        if (! $link) {
            return Response::denyAsNotFound();
        }

        if ($link->relationship_type !== 'principal') {
            return Response::deny('Somente a empresa principal pode editar o cadastro mestre deste condomínio.');
        }

        return $this->permissionService->can($user, $this->currentCompany(), 'update_condominiums', $condominium)
            ? Response::allow()
            : Response::deny('Você não tem permissão para editar este condomínio.');
    }

    public function delete(User $user, Condominium $condominium): Response
    {
        if ($user->isSuperAdmin()) {
            return $this->permissionService->can($user, $this->currentCompany(), 'deactivate_condominiums', $condominium)
                ? Response::allow()
                : Response::deny('Você não tem permissão para inativar este condomínio.');
        }

        $link = $this->currentCompanyLink($condominium);

        if (! $link) {
            return Response::denyAsNotFound();
        }

        if ($link->relationship_type !== 'principal') {
            return Response::deny('Somente a empresa principal pode inativar este condomínio.');
        }

        return $this->permissionService->can($user, $this->currentCompany(), 'deactivate_condominiums', $condominium)
            ? Response::allow()
            : Response::deny('Você não tem permissão para inativar este condomínio.');
    }

    protected function currentCompany(): mixed
    {
        return app()->bound('currentCompany') ? app('currentCompany') : null;
    }

    protected function currentCompanyLink(Condominium $condominium): ?CompanyCondominium
    {
        $company = $this->currentCompany();

        if (! $company) {
            return null;
        }

        return $condominium->companyLinks()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->first();
    }
}
