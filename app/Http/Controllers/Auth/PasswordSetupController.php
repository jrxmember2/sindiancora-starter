<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateFirstAccessPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordSetupController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Auth/FirstAccessPassword');
    }

    public function update(UpdateFirstAccessPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->string('password')->toString(),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Senha atualizada com sucesso. Seu acesso foi liberado.');
    }
}
