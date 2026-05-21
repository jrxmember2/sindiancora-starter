<?php

namespace App\Http\Requests\Tenant;

use App\Models\Condominium;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'cep' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:180'],
            'number' => ['nullable', 'string', 'max:40'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'size:2'],
            'mandate_start' => ['nullable', 'date'],
            'mandate_end' => ['nullable', 'date'],
            'administrator_name' => ['nullable', 'string', 'max:180'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $state = $this->input('state');
        $slug = $this->input('slug');
        $name = $this->input('name');

        $this->merge([
            'state' => $state ? Str::upper($state) : null,
            'slug' => $slug ?: ($name ? Str::slug($name) : null),
        ]);
    }
}
