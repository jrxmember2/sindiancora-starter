<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\LicenseRequest;
use App\Models\Company;
use App\Models\License;
use App\Models\Module;
use App\Services\Licensing\LicenseGuard;
use App\Services\Licensing\LicenseHistoryService;
use App\Services\Licensing\LicenseUsageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseHistoryService $licenseHistoryService,
        protected LicenseUsageService $licenseUsageService,
        protected LicenseGuard $licenseGuard,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Licenses/Index', [
            'licenses' => License::query()->with('company:id,name')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        $prefillCompanyId = request()->integer('company_id') ?: null;

        return Inertia::render('SuperAdmin/Licenses/Form', [
            'license' => null,
            'enabledModules' => [],
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
            'modules' => Module::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
            'usage' => null,
            'alerts' => [],
            'history' => [],
            'statusSummary' => null,
            'prefillCompanyId' => $prefillCompanyId,
        ]);
    }

    public function store(LicenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $license = License::query()->create(collect($validated)->except('modules')->all());
        $this->syncModules($license, $validated['modules'] ?? []);
        $license->load('modules', 'company');
        $this->licenseUsageService->sync($license);

        $this->licenseHistoryService->record(
            $license,
            $request->user(),
            'created',
            [],
            $this->licenseSnapshot($license),
            'Licença criada pelo superadmin.'
        );

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licença criada.');
    }

    public function edit(License $license): Response
    {
        $license->load(['modules', 'company', 'historyEntries.changedBy:id,name']);
        $this->licenseUsageService->sync($license);

        return Inertia::render('SuperAdmin/Licenses/Form', [
            'license' => $license,
            'enabledModules' => $license->modules->where('pivot.enabled', true)->pluck('id')->values(),
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
            'modules' => Module::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
            'usage' => $this->licenseGuard->usage($license->company),
            'alerts' => $this->licenseGuard->alerts($license->company),
            'statusSummary' => $this->licenseGuard->status($license->company),
            'history' => $license->historyEntries->take(12)->values()->map(fn ($entry) => [
                'id' => $entry->id,
                'change_type' => $entry->change_type,
                'notes' => $entry->notes,
                'created_at' => optional($entry->created_at)->toIso8601String(),
                'changed_by' => $entry->changedBy?->name,
                'old_data' => $entry->old_data,
                'new_data' => $entry->new_data,
            ]),
        ]);
    }

    public function update(LicenseRequest $request, License $license): RedirectResponse
    {
        $license->load('modules', 'company');
        $oldSnapshot = $this->licenseSnapshot($license);
        $validated = $request->validated();

        $license->update(collect($validated)->except('modules')->all());
        $this->syncModules($license, $validated['modules'] ?? []);
        $license->refresh()->load('modules', 'company');
        $this->licenseUsageService->sync($license);

        $newSnapshot = $this->licenseSnapshot($license);

        if ($oldSnapshot !== $newSnapshot) {
            $this->licenseHistoryService->record(
                $license,
                $request->user(),
                'updated',
                $oldSnapshot,
                $newSnapshot,
                'Licença atualizada pelo superadmin.'
            );
        }

        return redirect()->route('superadmin.licenses.index')->with('success', 'Licença atualizada.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $license->load('modules', 'company');
        $oldSnapshot = $this->licenseSnapshot($license);

        $license->update(['status' => 'canceled']);
        $license->refresh()->load('modules', 'company');
        $this->licenseUsageService->sync($license->company);

        $this->licenseHistoryService->record(
            $license,
            request()->user(),
            'canceled',
            $oldSnapshot,
            $this->licenseSnapshot($license),
            'Licença cancelada pelo superadmin.'
        );

        return back()->with('success', 'Licença cancelada.');
    }

    private function syncModules(License $license, array $moduleIds): void
    {
        $sync = [];

        foreach (Module::query()->pluck('id') as $moduleId) {
            $sync[$moduleId] = ['enabled' => in_array($moduleId, $moduleIds)];
        }

        $license->modules()->sync($sync);
    }

    private function licenseSnapshot(License $license): array
    {
        $license->loadMissing('modules');

        return [
            'company_id' => $license->company_id,
            'contract_number' => $license->contract_number,
            'status' => $license->status,
            'financial_status' => $license->financial_status,
            'billing_type' => $license->billing_type,
            'monthly_amount' => (string) $license->monthly_amount,
            'setup_amount' => (string) $license->setup_amount,
            'billing_day' => $license->billing_day,
            'starts_at' => optional($license->starts_at)->toDateString(),
            'ends_at' => optional($license->ends_at)->toDateString(),
            'renews_at' => optional($license->renews_at)->toDateString(),
            'max_condominiums' => $license->max_condominiums,
            'max_internal_users' => $license->max_internal_users,
            'max_storage_mb' => $license->max_storage_mb,
            'max_whatsapp_instances' => $license->max_whatsapp_instances,
            'monthly_ai_credits' => $license->monthly_ai_credits,
            'allow_overage' => $license->allow_overage,
            'block_new_records_on_limit' => $license->block_new_records_on_limit,
            'read_only_when_expired' => $license->read_only_when_expired,
            'auto_suspend_when_overdue' => $license->auto_suspend_when_overdue,
            'notes' => $license->notes,
            'modules' => $license->modules
                ->where('pivot.enabled', true)
                ->sortBy('key')
                ->pluck('key')
                ->values()
                ->all(),
        ];
    }
}
