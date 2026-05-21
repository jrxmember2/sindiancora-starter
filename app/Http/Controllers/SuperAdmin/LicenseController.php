<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\LicenseRequest;
use App\Models\Company;
use App\Models\License;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LicenseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Licenses/Index', [
            'licenses' => License::query()->with('company:id,name')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SuperAdmin/Licenses/Form', [
            'license' => null,
            'enabledModules' => [],
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
            'modules' => Module::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function store(LicenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $license = License::create($validated);
        $this->syncModules($license, $validated['modules'] ?? []);

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licenca criada.');
    }

    public function edit(License $license): Response
    {
        $license->load('modules');

        return Inertia::render('SuperAdmin/Licenses/Form', [
            'license' => $license,
            'enabledModules' => $license->modules->where('pivot.enabled', true)->pluck('id')->values(),
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
            'modules' => Module::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function update(LicenseRequest $request, License $license): RedirectResponse
    {
        $validated = $request->validated();
        $license->update($validated);
        $this->syncModules($license, $validated['modules'] ?? []);

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licenca atualizada.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $license->update(['status' => 'canceled']);

        return back()->with('success', 'Licenca cancelada.');
    }

    private function syncModules(License $license, array $moduleIds): void
    {
        $sync = [];

        foreach (Module::query()->pluck('id') as $moduleId) {
            $sync[$moduleId] = ['enabled' => in_array($moduleId, $moduleIds)];
        }

        $license->modules()->sync($sync);
    }
}
