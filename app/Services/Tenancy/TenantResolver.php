<?php

namespace App\Services\Tenancy;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Condominium;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TenantResolver
{
    public function bindCurrentCompany(Request $request): ?Company
    {
        $company = $this->resolveCurrentCompany($request);

        app()->instance('currentCompany', $company);

        return $company;
    }

    public function resolveCurrentCompany(Request $request): ?Company
    {
        $user = $request->user();

        if (! $user) {
            $this->forgetCurrentCompanyFromSession($request);

            return null;
        }

        $company = null;
        $companyId = $request->hasSession() ? $request->session()->get('current_company_id') : null;

        if ($companyId) {
            $company = $user->isSuperAdmin()
                ? Company::query()->withoutGlobalScopes()->find($companyId)
                : $this->activeCompaniesQuery($user)->whereKey($companyId)->first();
        }

        if (! $company && ! $user->isSuperAdmin()) {
            $company = $this->activeCompaniesQuery($user)->first();
        }

        if ($request->hasSession()) {
            if ($company) {
                $request->session()->put('current_company_id', $company->id);
            } else {
                $this->forgetCurrentCompanyFromSession($request);
            }
        }

        return $company;
    }

    public function companiesForUser(User $user): Collection
    {
        if ($user->isSuperAdmin()) {
            return Company::query()
                ->withoutGlobalScopes()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'status']);
        }

        return $this->activeCompaniesQuery($user)
            ->orderBy('companies.name')
            ->get(['companies.id', 'companies.name', 'companies.slug', 'companies.status']);
    }

    public function currentCompanyUser(User $user, Company $company): ?CompanyUser
    {
        if ($user->isSuperAdmin()) {
            return null;
        }

        return CompanyUser::query()
            ->where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
    }

    public function accessibleCondominiumIds(User $user, Company $company): ?Collection
    {
        if ($user->isSuperAdmin()) {
            return null;
        }

        $companyUser = $this->currentCompanyUser($user, $company);

        if (! $companyUser) {
            return collect();
        }

        $linkedIds = Condominium::query()
            ->withoutGlobalScopes()
            ->whereHas('companyLinks', function (Builder $query) use ($company) {
                $query
                    ->where('company_id', $company->id)
                    ->where('status', 'active');
            })
            ->pluck('condominiums.id');

        $ids = $companyUser->condominiums()->pluck('condominiums.id');

        if ($ids->isEmpty()) {
            return $linkedIds->values();
        }

        return $linkedIds->intersect($ids)->values();
    }

    public function accessibleCondominiumsQuery(User $user, Company $company): Builder
    {
        $query = Condominium::query()
            ->withoutGlobalScopes()
            ->whereHas('companyLinks', function (Builder $linkQuery) use ($company) {
                $linkQuery
                    ->where('company_id', $company->id)
                    ->where('status', 'active');
            });
        $accessibleIds = $this->accessibleCondominiumIds($user, $company);

        if ($accessibleIds !== null) {
            $query->whereKey($accessibleIds->all());
        }

        return $query;
    }

    public function scopeByAccessibleCondominiums(
        Builder $query,
        User $user,
        Company $company,
        string $column = 'condominium_id',
        bool $includeNull = false,
    ): Builder {
        $accessibleIds = $this->accessibleCondominiumIds($user, $company);

        if ($accessibleIds === null) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($accessibleIds, $column, $includeNull) {
            $inner->whereIn($column, $accessibleIds->all());

            if ($includeNull) {
                $inner->orWhereNull($column);
            }
        });
    }

    public function scopeByIssueAssignments(
        Builder $query,
        User $user,
        Company $company,
        string $responsibleColumn = 'responsible_user_id',
    ): Builder {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $companyUser = $this->currentCompanyUser($user, $company);

        if (! $companyUser) {
            return $query->whereRaw('1 = 0');
        }

        if (! $companyUser->only_responsible_issues) {
            return $query;
        }

        return $query->where($responsibleColumn, $user->id);
    }

    public function canAccessCondominium(User $user, Company $company, int|string|null $condominiumId): bool
    {
        if ($condominiumId === null) {
            return true;
        }

        $accessibleIds = $this->accessibleCondominiumIds($user, $company);

        return $accessibleIds === null || $accessibleIds->contains((int) $condominiumId);
    }

    public function canAccessIssue(User $user, Company $company, Issue $issue): bool
    {
        if (! $this->canAccessCondominium($user, $company, $issue->condominium_id)) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $companyUser = $this->currentCompanyUser($user, $company);

        if (! $companyUser) {
            return false;
        }

        if (! $companyUser->only_responsible_issues) {
            return true;
        }

        return (int) $issue->responsible_user_id === (int) $user->id;
    }

    public function canSwitchToCompany(User $user, int $companyId): bool
    {
        if ($user->isSuperAdmin()) {
            return Company::query()->withoutGlobalScopes()->whereKey($companyId)->exists();
        }

        return $this->activeCompaniesQuery($user)->whereKey($companyId)->exists();
    }

    protected function activeCompaniesQuery(User $user): BelongsToMany
    {
        return $user->companies()
            ->wherePivot('status', 'active')
            ->where('companies.status', 'active');
    }

    protected function forgetCurrentCompanyFromSession(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->forget('current_company_id');
        }
    }
}
