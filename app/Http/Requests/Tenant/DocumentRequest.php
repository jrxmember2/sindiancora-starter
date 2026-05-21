<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Validation\Rule;

class DocumentRequest extends TenantFormRequest
{
    public function rules(): array
    {
        return [
            'condominium_id' => ['nullable', 'integer', $this->existsInCurrentCompany('condominiums')],
            'title' => ['required', 'string', 'max:180'],
            'document_type' => ['required', 'string', 'max:80'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date'],
            'renewal_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['valido', 'vencido', 'proximo_vencimento', 'sem_vigencia'])],
            'available_to_residents' => ['boolean'],
            'added_to_ai_assistant' => ['boolean'],
            'observation' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'available_to_residents' => $this->boolean('available_to_residents'),
            'added_to_ai_assistant' => $this->boolean('added_to_ai_assistant'),
        ]);
    }
}
