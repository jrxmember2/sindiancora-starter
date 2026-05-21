<?php

namespace App\Http\Requests\Tenant;

class SupplierRequest extends TenantFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'responsible_name' => ['nullable', 'string', 'max:160'],
            'mobile' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'string', 'max:180'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'cep' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:180'],
            'number' => ['nullable', 'string', 'max:40'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'size:2'],
            'country' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
            'active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'state' => $this->filled('state') ? strtoupper((string) $this->input('state')) : null,
        ]);
    }
}
