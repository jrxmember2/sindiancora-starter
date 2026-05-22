<?php

namespace App\Policies;

use App\Models\CompanyUser;
use App\Models\User;
use App\Services\Permissions\CompanyPermissionService;
use Illuminate\Auth\Access\Response;

class CompanyUserPolicy
{
    public function __construct(protected CompanyPermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->can($user, $this->currentCompany(), 'view_company_users');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->can($user, $this->currentCompany(), 'create_company_users');
    }

    public function update(User $user, CompanyUser $companyUser): Response
    {
        if ((int) $companyUser->company_id !== (int) $this->currentCompany()?->id) {
            return Response::denyAsNotFound();
        }

        return $this->permissionService->can($user, $this->currentCompany(), 'update_company_users', $companyUser)
            ? Response::allow()
            : Response::deny('Você não tem permissão para editar este usuário interno.');
    }

    public function delete(User $user, CompanyUser $companyUser): Response
    {
        if ((int) $companyUser->company_id !== (int) $this->currentCompany()?->id) {
            return Response::denyAsNotFound();
        }

        return $this->permissionService->can($user, $this->currentCompany(), 'deactivate_company_users', $companyUser)
            ? Response::allow()
            : Response::deny('Você não tem permissão para inativar este usuário interno.');
    }

    protected function currentCompany(): mixed
    {
        return app()->bound('currentCompany') ? app('currentCompany') : null;
    }
}
