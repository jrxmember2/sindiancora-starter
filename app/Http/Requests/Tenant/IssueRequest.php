<?php

namespace App\Http\Requests\Tenant;

use App\Services\Tenancy\TenantResolver;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class IssueRequest extends TenantFormRequest
{
    public function rules(): array
    {
        return [
            'condominium_id' => ['required', 'integer', $this->existsInCurrentCompany('condominiums')],
            'subject' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['pendente', 'em_andamento', 'aguardando_assembleia', 'finalizado', 'cancelado'])],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta', 'urgente'])],
            'deadline_at' => ['nullable', 'date'],
            'shared_with_residents' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shared_with_residents' => $this->boolean('shared_with_residents'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            $company = app()->bound('currentCompany') ? app('currentCompany') : null;

            if (! $user || ! $company) {
                return;
            }

            if (! app(TenantResolver::class)->canAccessCondominium($user, $company, $this->input('condominium_id'))) {
                $validator->errors()->add('condominium_id', 'Você não pode operar neste condomínio.');
            }
        });
    }
}
