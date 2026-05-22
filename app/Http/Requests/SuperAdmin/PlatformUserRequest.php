<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PlatformUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var User|null $platformUser */
        $platformUser = $this->route('platformUser');

        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($platformUser?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [
                $platformUser ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'must_change_password' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'must_change_password' => $this->boolean('must_change_password'),
        ]);
    }
}
