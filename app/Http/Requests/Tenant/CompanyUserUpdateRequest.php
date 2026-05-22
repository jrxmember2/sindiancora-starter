<?php

namespace App\Http\Requests\Tenant;

use App\Models\CompanyUser;
use App\Models\User;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CompanyUserUpdateRequest extends TenantFormRequest
{
    public function authorize(): bool
    {
        /** @var CompanyUser|null $companyUser */
        $companyUser = $this->route('companyUser');

        return $companyUser ? ($this->user()?->can('update', $companyUser) ?? false) : false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(config('company_permissions.roles', [])))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'can_access_whatsapp' => ['boolean'],
            'only_responsible_issues' => ['boolean'],
            'condominium_ids' => ['nullable', 'array'],
            'condominium_ids.*' => ['integer', $this->existsInCurrentCompany('condominiums')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'can_access_whatsapp' => $this->boolean('can_access_whatsapp'),
            'only_responsible_issues' => $this->boolean('only_responsible_issues'),
            'condominium_ids' => array_values(array_unique($this->input('condominium_ids', []))),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var CompanyUser|null $companyUser */
            $companyUser = $this->route('companyUser');
            $company = app()->bound('currentCompany') ? app('currentCompany') : null;

            if (! $companyUser || ! $company) {
                return;
            }

            $emailOwner = User::query()
                ->where('email', $this->input('email'))
                ->whereKeyNot($companyUser->user_id)
                ->exists();

            if ($emailOwner) {
                $validator->errors()->add('email', 'Já existe outro usuário com este e-mail.');
            }

            if (
                $this->input('status') === 'active'
                && $companyUser->status !== 'active'
                && ! app(LicenseGuard::class)->canCreateInternalUser($company)
            ) {
                $validator->errors()->add('status', 'O limite de usuários internos ativos desta licença foi atingido.');
            }
        });
    }
}
