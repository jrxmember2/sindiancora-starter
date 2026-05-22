<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

abstract class TenantFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function companyId(): ?int
    {
        return app()->bound('currentCompany') && app('currentCompany')
            ? (int) app('currentCompany')->id
            : null;
    }

    protected function existsInCurrentCompany(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)->where(function (Builder $query) {
            if ($this->companyId()) {
                $query->where('company_id', $this->companyId());
            }
        });
    }

    protected function uniqueInCurrentCompany(string $table, string $column, mixed $ignore = null): Unique
    {
        return Rule::unique($table, $column)
            ->where(function (Builder $query) {
                if ($this->companyId()) {
                    $query->where('company_id', $this->companyId());
                }
            })
            ->ignore($ignore);
    }

    protected function existsInAccessibleCondominiums(string $column = 'id'): Exists
    {
        $companyId = $this->companyId();
        $user = $this->user();

        return Rule::exists('condominiums', $column)->where(function (Builder $query) use ($companyId, $user) {
            if (! $companyId) {
                return;
            }

            $query->whereExists(function (Builder $subQuery) use ($companyId) {
                $subQuery
                    ->selectRaw('1')
                    ->from('company_condominiums')
                    ->whereColumn('company_condominiums.condominium_id', 'condominiums.id')
                    ->where('company_condominiums.company_id', $companyId)
                    ->where('company_condominiums.status', 'active');
            });

            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            $membership = app(\App\Services\Tenancy\TenantResolver::class)->currentCompanyUser($user, app('currentCompany'));

            if (! $membership) {
                $query->whereRaw('1 = 0');

                return;
            }

            $assignedIds = $membership->condominiums()->pluck('condominiums.id');

            if ($assignedIds->isNotEmpty()) {
                $query->whereIn('condominiums.id', $assignedIds->all());
            }
        });
    }
}
