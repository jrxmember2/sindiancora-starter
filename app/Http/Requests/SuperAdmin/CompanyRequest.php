<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\Company;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var Company|null $company */
        $company = $this->route('company');
        $primaryUserId = $company?->primaryCompanyUser?->user_id;

        return [
            'name' => ['required', 'string', 'max:160'],
            'document' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'responsible_name' => ['nullable', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('companies', 'slug')->ignore($company?->id),
            ],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'primary_user_name' => ['required', 'string', 'max:160'],
            'primary_user_email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($primaryUserId)],
            'primary_user_phone' => ['nullable', 'string', 'max:40'],
            'primary_user_password' => [
                $primaryUserId ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'primary_user_force_password_reset' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'primary_user_force_password_reset' => $this->boolean('primary_user_force_password_reset', true),
        ]);
    }
}
