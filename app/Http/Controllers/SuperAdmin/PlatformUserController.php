<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\PlatformUserRequest;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PlatformUserController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger)
    {
    }

    public function index(): Response
    {
        return Inertia::render('SuperAdmin/PlatformUsers/Index', [
            'items' => User::query()
                ->where('is_superadmin', true)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SuperAdmin/PlatformUsers/Form', ['item' => null]);
    }

    public function store(PlatformUserRequest $request): RedirectResponse
    {
        $platformUser = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'password' => $request->string('password')->toString(),
            'status' => $request->string('status')->toString(),
            'is_superadmin' => true,
            'must_change_password' => $request->boolean('must_change_password'),
        ]);

        $this->auditLogger->record(
            action: 'platform_user.created',
            actor: $request->user(),
            auditable: $platformUser,
            newValues: $platformUser->only(['id', 'name', 'email', 'status', 'must_change_password']),
        );

        return redirect()->route('superadmin.platform-users.index')->with('success', 'Usuário da plataforma criado com sucesso.');
    }

    public function edit(User $platformUser): Response
    {
        abort_unless($platformUser->isSuperAdmin(), 404);

        return Inertia::render('SuperAdmin/PlatformUsers/Form', [
            'item' => $platformUser->only(['id', 'name', 'email', 'phone', 'status', 'must_change_password']),
        ]);
    }

    public function update(PlatformUserRequest $request, User $platformUser): RedirectResponse
    {
        abort_unless($platformUser->isSuperAdmin(), 404);

        if ($platformUser->id === $request->user()->id && $request->input('status') !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'Você não pode inativar o seu próprio usuário da plataforma nesta tela.',
            ]);
        }

        if ($platformUser->id !== $request->user()->id && $platformUser->status === 'active' && $request->input('status') !== 'active') {
            $this->guardLastActiveSuperadmin($platformUser);
        }

        $before = $platformUser->only(['id', 'name', 'email', 'status', 'must_change_password']);

        $platformUser->update([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'status' => $request->string('status')->toString(),
            'must_change_password' => $request->boolean('must_change_password'),
        ]);

        if ($request->filled('password')) {
            $platformUser->update(['password' => $request->string('password')->toString()]);
        }

        $this->auditLogger->record(
            action: 'platform_user.updated',
            actor: $request->user(),
            auditable: $platformUser,
            oldValues: $before,
            newValues: $platformUser->only(['id', 'name', 'email', 'status', 'must_change_password']),
        );

        return redirect()->route('superadmin.platform-users.index')->with('success', 'Usuário da plataforma atualizado com sucesso.');
    }

    public function destroy(Request $request, User $platformUser): RedirectResponse
    {
        abort_unless($platformUser->isSuperAdmin(), 404);

        if ($platformUser->id === $request->user()->id) {
            throw ValidationException::withMessages([
                'platform_user' => 'Você não pode inativar o seu próprio usuário da plataforma nesta tela.',
            ]);
        }

        $this->guardLastActiveSuperadmin($platformUser);

        $before = $platformUser->only(['id', 'name', 'email', 'status', 'must_change_password']);

        $platformUser->update(['status' => 'inactive']);

        $this->auditLogger->record(
            action: 'platform_user.deactivated',
            actor: $request->user(),
            auditable: $platformUser,
            oldValues: $before,
            newValues: $platformUser->only(['id', 'name', 'email', 'status', 'must_change_password']),
        );

        return back()->with('success', 'Usuário da plataforma inativado com sucesso.');
    }

    protected function guardLastActiveSuperadmin(User $platformUser): void
    {
        if ($platformUser->status !== 'active') {
            return;
        }

        $hasAnother = User::query()
            ->where('is_superadmin', true)
            ->where('status', 'active')
            ->where('id', '!=', $platformUser->id)
            ->exists();

        if (! $hasAnother) {
            throw ValidationException::withMessages([
                'status' => 'A plataforma precisa manter pelo menos um superadmin ativo.',
            ]);
        }
    }
}
