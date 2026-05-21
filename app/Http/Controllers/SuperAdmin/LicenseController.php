<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\License;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $license = License::create($this->validated($request));
        $this->syncModules($license, $request->input('modules', []));

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licença criada.');
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

    public function update(Request $request, License $license): RedirectResponse
    {
        $license->update($this->validated($request));
        $this->syncModules($license, $request->input('modules', []));

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licença atualizada.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $license->update(['status' => 'canceled']);

        return back()->with('success', 'Licença cancelada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'contract_number' => ['required', 'string', 'max:80'],
            'status' => ['required', 'in:trial,active,pending,expired,suspended,canceled,blocked,read_only'],
            'financial_status' => ['required', 'in:current,due,overdue,negotiated,suspended,canceled'],
            'billing_type' => ['required', 'in:monthly,quarterly,yearly,custom'],
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
        ]);
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
