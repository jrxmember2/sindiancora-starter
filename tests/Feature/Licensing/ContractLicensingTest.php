<?php

namespace Tests\Feature\Licensing;

use App\Models\Company;
use App\Models\Condominium;
use App\Models\License;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContractLicensingTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_license_with_history_and_usage_snapshot(): void
    {
        $superadmin = User::factory()->create([
            'is_superadmin' => true,
        ]);

        $company = Company::factory()->create();
        $dashboardModule = Module::query()->create([
            'key' => 'dashboard',
            'name' => 'Dashboard',
            'description' => 'Visao geral',
            'category' => 'Core',
            'active' => true,
        ]);
        $issuesModule = Module::query()->create([
            'key' => 'chamados',
            'name' => 'Chamados',
            'description' => 'Gestao de chamados',
            'category' => 'Operacional',
            'active' => true,
        ]);

        $this->actingAs($superadmin)
            ->post('/superadmin/licenses', [
                'company_id' => $company->id,
                'contract_number' => 'CTR-2026-001',
                'status' => 'active',
                'financial_status' => 'current',
                'billing_type' => 'monthly',
                'monthly_amount' => 249.90,
                'setup_amount' => 990,
                'billing_day' => 10,
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->addYear()->toDateString(),
                'renews_at' => now()->addYear()->toDateString(),
                'max_condominiums' => 5,
                'max_internal_users' => 8,
                'max_storage_mb' => 2048,
                'max_whatsapp_instances' => 1,
                'monthly_ai_credits' => 100,
                'allow_overage' => true,
                'block_new_records_on_limit' => false,
                'read_only_when_expired' => true,
                'auto_suspend_when_overdue' => false,
                'notes' => 'Contrato piloto',
                'modules' => [$dashboardModule->id, $issuesModule->id],
            ])
            ->assertRedirect('/superadmin/licenses');

        $license = License::query()->firstOrFail();

        $this->assertDatabaseHas('license_history', [
            'license_id' => $license->id,
            'change_type' => 'created',
        ]);

        $this->assertDatabaseHas('license_usage', [
            'company_id' => $company->id,
            'license_id' => $license->id,
            'active_condominiums' => 0,
            'active_internal_users' => 0,
        ]);

        $this->assertDatabaseHas('license_modules', [
            'license_id' => $license->id,
            'module_id' => $dashboardModule->id,
            'enabled' => true,
        ]);

        $this->assertDatabaseHas('license_modules', [
            'license_id' => $license->id,
            'module_id' => $issuesModule->id,
            'enabled' => true,
        ]);
    }

    public function test_tenant_license_overview_exposes_usage_modules_and_alerts(): void
    {
        [$user, $company, $license] = $this->createLicensedTenantUser(['chamados', 'documentos'], [
            'ends_at' => now()->addDays(20)->toDateString(),
            'max_condominiums' => 1,
            'max_internal_users' => 2,
        ]);

        Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condominio Centro',
            'status' => 'active',
            'slug' => 'condominio-centro',
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/license')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/License/Show')
                ->where('license.contract_number', $license->contract_number)
                ->has('license.modules', 2)
                ->where('usage.condominiums.used', 1)
                ->where('usage.condominiums.limit', 1)
                ->where('usage.internal_users.used', 1)
                ->has('alerts', 2)
                ->where('alerts.0.title', 'Licença perto do vencimento'));
    }

    public function test_disabled_module_route_redirects_to_dashboard(): void
    {
        [$user, $company] = $this->createLicensedTenantUser(['documentos']);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/issues')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_read_only_license_allows_overview_but_blocks_writes(): void
    {
        [$user, $company] = $this->createLicensedTenantUser(['chamados'], [
            'status' => 'read_only',
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/license')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/License/Show')
                ->where('statusSummary.code', 'read_only'));

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->post('/app/issues', [])
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');

        $this->assertDatabaseCount('issues', 0);
    }

    private function createLicensedTenantUser(array $moduleKeys = ['chamados'], array $licenseOverrides = []): array
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $license = License::query()->create(array_merge([
            'company_id' => $company->id,
            'contract_number' => 'CTR-001',
            'status' => 'active',
            'financial_status' => 'current',
            'billing_type' => 'monthly',
            'monthly_amount' => 100,
            'setup_amount' => 0,
            'billing_day' => 5,
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addYear()->toDateString(),
            'renews_at' => now()->addYear()->toDateString(),
            'max_condominiums' => 10,
            'max_internal_users' => 10,
            'max_storage_mb' => 1024,
            'max_whatsapp_instances' => 1,
            'monthly_ai_credits' => 100,
            'allow_overage' => false,
            'block_new_records_on_limit' => true,
            'read_only_when_expired' => true,
            'auto_suspend_when_overdue' => false,
        ], $licenseOverrides));

        foreach ($moduleKeys as $moduleKey) {
            $module = Module::query()->create([
                'key' => $moduleKey,
                'name' => ucfirst(str_replace('_', ' ', $moduleKey)),
                'description' => "Modulo {$moduleKey}",
                'category' => 'Operacional',
                'active' => true,
            ]);

            $license->modules()->attach($module->id, ['enabled' => true]);
        }

        return [$user, $company, $license];
    }
}
