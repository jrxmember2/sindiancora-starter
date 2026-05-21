<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', Rule::exists('companies', 'id')],
            'contract_number' => ['required', 'string', 'max:80'],
            'status' => ['required', Rule::in(['trial', 'active', 'pending', 'expired', 'suspended', 'canceled', 'blocked', 'read_only'])],
            'financial_status' => ['required', Rule::in(['current', 'due', 'overdue', 'negotiated', 'suspended', 'canceled'])],
            'billing_type' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'custom'])],
            'monthly_amount' => ['nullable', 'numeric', 'min:0'],
            'setup_amount' => ['nullable', 'numeric', 'min:0'],
            'billing_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'renews_at' => ['nullable', 'date'],
            'max_condominiums' => ['required', 'integer', 'min:0'],
            'max_internal_users' => ['required', 'integer', 'min:0'],
            'max_storage_mb' => ['required', 'integer', 'min:0'],
            'max_whatsapp_instances' => ['required', 'integer', 'min:0'],
            'monthly_ai_credits' => ['required', 'integer', 'min:0'],
            'allow_overage' => ['boolean'],
            'block_new_records_on_limit' => ['boolean'],
            'read_only_when_expired' => ['boolean'],
            'auto_suspend_when_overdue' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['integer', Rule::exists('modules', 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'allow_overage' => $this->boolean('allow_overage'),
            'block_new_records_on_limit' => $this->boolean('block_new_records_on_limit'),
            'read_only_when_expired' => $this->boolean('read_only_when_expired'),
            'auto_suspend_when_overdue' => $this->boolean('auto_suspend_when_overdue'),
            'modules' => $this->input('modules', []),
        ]);
    }
}
