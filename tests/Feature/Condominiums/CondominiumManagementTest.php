<?php

namespace Tests\Feature\Condominiums;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Condominium;
use App\Models\License;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CondominiumManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_active_condominium_with_logo_and_audit_log(): void
    {
        Storage::fake('public');
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxCondominiums: 2);

        $response = $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->post('/app/condominiums', [
                'name' => 'Residencial Atlântico',
                'document' => '12.345.678/0001-90',
                'email' => 'contato@atlantico.test',
                'phone' => '1133334444',
                'slug' => 'residencial-atlantico',
                'status' => 'active',
                'logo' => $this->makeTinyPngUpload(),
                'city' => 'Santos',
                'state' => 'sp',
                'administrator_name' => 'Serratech Gestão',
            ]);

        $response->assertRedirect('/app/condominiums');

        $condominium = Condominium::query()->firstOrFail();

        $this->assertDatabaseHas('condominiums', [
            'id' => $condominium->id,
            'company_id' => $company->id,
            'name' => 'Residencial Atlântico',
            'status' => 'active',
            'state' => 'SP',
        ]);

        Storage::disk('public')->assertExists($condominium->getRawOriginal('logo_url'));

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'action' => 'condominium.created',
            'auditable_type' => Condominium::class,
            'auditable_id' => $condominium->id,
        ]);

        $this->assertDatabaseHas('license_usage', [
            'company_id' => $company->id,
            'active_condominiums' => 1,
        ]);
    }

    public function test_inactive_condominium_can_be_created_even_when_active_limit_is_reached(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxCondominiums: 1);

        Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Ativo',
            'status' => 'active',
            'slug' => 'condominio-ativo',
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->post('/app/condominiums', [
                'name' => 'Condomínio Arquivado',
                'status' => 'inactive',
                'slug' => 'condominio-arquivado',
            ])
            ->assertRedirect('/app/condominiums');

        $this->assertDatabaseHas('condominiums', [
            'company_id' => $company->id,
            'name' => 'Condomínio Arquivado',
            'status' => 'inactive',
        ]);
    }

    public function test_reactivating_inactive_condominium_respects_license_limit(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxCondominiums: 1);

        Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Ativo',
            'status' => 'active',
            'slug' => 'condominio-ativo',
        ]);

        $inactiveCondominium = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Inativo',
            'status' => 'inactive',
            'slug' => 'condominio-inativo',
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->from("/app/condominiums/{$inactiveCondominium->id}/edit")
            ->put("/app/condominiums/{$inactiveCondominium->id}", [
                'name' => 'Condomínio Inativo',
                'status' => 'active',
                'slug' => 'condominio-inativo',
            ])
            ->assertRedirect("/app/condominiums/{$inactiveCondominium->id}/edit")
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('condominiums', [
            'id' => $inactiveCondominium->id,
            'status' => 'inactive',
        ]);
    }

    public function test_destroy_inactivates_condominium_without_deleting_it(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxCondominiums: 2);

        $condominium = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio para Inativação',
            'status' => 'active',
            'slug' => 'condominio-para-inativacao',
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->delete("/app/condominiums/{$condominium->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('condominiums', [
            'id' => $condominium->id,
            'status' => 'inactive',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'action' => 'condominium.inactivated',
            'auditable_type' => Condominium::class,
            'auditable_id' => $condominium->id,
        ]);

        $this->assertDatabaseHas('license_usage', [
            'company_id' => $company->id,
            'active_condominiums' => 0,
        ]);
    }

    public function test_non_admin_cannot_access_condominium_management_screen(): void
    {
        [$gestor, $company] = $this->createTenantUserWithCompany(role: 'gestor', moduleKeys: ['configuracoes']);

        $this->actingAs($gestor)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/condominiums')
            ->assertForbidden();
    }

    public function test_index_filters_condominiums_by_search_and_status(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxCondominiums: 3);

        Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Residencial Vitória',
            'status' => 'active',
            'slug' => 'residencial-vitoria',
            'city' => 'Vitória',
        ]);

        Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Residencial Serra',
            'status' => 'inactive',
            'slug' => 'residencial-serra',
            'city' => 'Serra',
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/condominiums?search=Vit%C3%B3ria&status=active')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/Condominiums/Index')
                ->where('filters.search', 'Vitória')
                ->where('filters.status', 'active')
                ->has('items.data', 1)
                ->where('items.data.0.name', 'Residencial Vitória'));
    }

    private function createTenantUserWithCompany(
        string $role = 'admin',
        int $maxCondominiums = 10,
        array $moduleKeys = ['configuracoes']
    ): array {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        CompanyUser::query()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'role' => $role,
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $license = License::query()->create([
            'company_id' => $company->id,
            'contract_number' => 'CTR-CONDO-001',
            'status' => 'active',
            'financial_status' => 'current',
            'billing_type' => 'monthly',
            'monthly_amount' => 100,
            'setup_amount' => 0,
            'billing_day' => 5,
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addYear()->toDateString(),
            'renews_at' => now()->addYear()->toDateString(),
            'max_condominiums' => $maxCondominiums,
            'max_internal_users' => 10,
            'max_storage_mb' => 1024,
            'max_whatsapp_instances' => 1,
            'monthly_ai_credits' => 100,
            'allow_overage' => false,
            'block_new_records_on_limit' => true,
            'read_only_when_expired' => true,
            'auto_suspend_when_overdue' => false,
        ]);

        foreach ($moduleKeys as $moduleKey) {
            $module = Module::query()->create([
                'key' => $moduleKey,
                'name' => ucfirst(str_replace('_', ' ', $moduleKey)),
                'description' => "Módulo {$moduleKey}",
                'category' => 'Configurações',
                'active' => true,
            ]);

            $license->modules()->attach($module->id, ['enabled' => true]);
        }

        return [$user, $company];
    }

    private function makeTinyPngUpload(string $name = 'logo.png'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'condo-logo-');

        file_put_contents(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO3ZsS8AAAAASUVORK5CYII=')
        );

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
