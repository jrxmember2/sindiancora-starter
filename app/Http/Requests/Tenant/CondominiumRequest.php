<?php

namespace App\Http\Requests\Tenant;

use App\Models\Condominium;
use App\Services\Licensing\LicenseGuard;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CondominiumRequest extends TenantFormRequest
{
    public function rules(): array
    {
        /** @var Condominium|null $condominium */
        $condominium = $this->route('condominium');

        return [
            'name' => ['required', 'string', 'max:180'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'slug' => [
                'nullable',
                'string',
                'max:160',
                $this->uniqueInCurrentCompany('condominiums', 'slug', $condominium?->id),
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_logo' => ['boolean'],
            'cep' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:180'],
            'number' => ['nullable', 'string', 'max:40'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'size:2'],
            'mandate_start' => ['nullable', 'date'],
            'mandate_end' => ['nullable', 'date', 'after_or_equal:mandate_start'],
            'administrator_name' => ['nullable', 'string', 'max:180'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $state = $this->input('state');
        $slug = $this->input('slug');
        $name = $this->input('name');
        $email = $this->input('email');

        $this->merge([
            'state' => $state ? Str::upper(trim((string) $state)) : null,
            'slug' => $slug ?: ($name ? Str::slug($name) : null),
            'email' => $email ? Str::lower(trim((string) $email)) : null,
            'remove_logo' => $this->boolean('remove_logo'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var Condominium|null $condominium */
            $condominium = $this->route('condominium');
            $company = app()->bound('currentCompany') ? app('currentCompany') : null;
            $wantsActiveStatus = $this->input('status') === 'active';
            $isCreatingActive = ! $condominium && $wantsActiveStatus;
            $isReactivating = $condominium && $condominium->status !== 'active' && $wantsActiveStatus;

            if (($isCreatingActive || $isReactivating) && ! app(LicenseGuard::class)->canCreateCondominium($company)) {
                $validator->errors()->add('status', 'O limite de condomínios ativos desta licença foi atingido.');
            }
        });
    }
}
