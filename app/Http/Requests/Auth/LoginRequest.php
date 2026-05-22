<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $credentials = $this->safe()->only(['email', 'password']);

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha inválidos.',
            ]);
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($user->status !== 'active') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Seu usuário está inativo no momento.',
            ]);
        }

        if (! $user->isSuperAdmin() && ! $user->companies()->wherePivot('status', 'active')->where('companies.status', 'active')->exists()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Seu acesso não possui vínculo ativo com nenhuma empresa.',
            ]);
        }
    }
}
