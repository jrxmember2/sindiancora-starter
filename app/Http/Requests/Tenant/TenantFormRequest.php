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
}
