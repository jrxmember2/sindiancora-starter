<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForceCondominiumTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'target_company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'decision_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
