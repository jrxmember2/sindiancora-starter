<?php

namespace Tests\Feature\Tenancy;

use App\Models\Company;
use App\Models\Condominium;
use App\Models\Issue;
use App\Models\License;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_user_sees_only_issues_from_the_active_company(): void
    {
        [$user, $companyA] = $this->createLicensedTenantUser();
        $companyB = Company::factory()->create();

        $condominiumA = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condominio Alfa',
            'status' => 'active',
            'slug' => 'condominio-alfa',
        ]);

        $condominiumB = Condominium::query()->create([
            'company_id' => $companyB->id,
            'name' => 'Condominio Beta',
            'status' => 'active',
            'slug' => 'condominio-beta',
        ]);

        Issue::query()->create([
            'company_id' => $companyA->id,
            'condominium_id' => $condominiumA->id,
            'subject' => 'Chamado empresa A',
            'description' => 'Visivel para o tenant correto.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        Issue::query()->create([
            'company_id' => $companyB->id,
            'condominium_id' => $condominiumB->id,
            'subject' => 'Chamado empresa B',
            'description' => 'Nao pode vazar para outra empresa.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->get('/app/issues')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/Issues/Index')
                ->has('issues.data', 1)
                ->where('issues.data.0.subject', 'Chamado empresa A'));
    }

    public function test_company_user_cannot_switch_to_an_unrelated_company(): void
    {
        [$user, $companyA] = $this->createLicensedTenantUser();
        $companyB = Company::factory()->create();

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->post('/trocar-empresa', ['company_id' => $companyB->id])
            ->assertForbidden();
    }

    public function test_company_switch_allows_only_active_memberships(): void
    {
        [$user, $companyA] = $this->createLicensedTenantUser();
        $companyB = Company::factory()->create(['status' => 'active']);
        $companyC = Company::factory()->create(['status' => 'inactive']);

        $user->companies()->attach($companyB->id, [
            'role' => 'gestor',
            'status' => 'inactive',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $user->companies()->attach($companyC->id, [
            'role' => 'gestor',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->post('/trocar-empresa', ['company_id' => $companyB->id])
            ->assertForbidden();

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->post('/trocar-empresa', ['company_id' => $companyC->id])
            ->assertForbidden();

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('tenant.companies', 1)
                ->where('tenant.companies.0.id', $companyA->id));
    }

    public function test_route_access_does_not_resolve_issue_from_another_company(): void
    {
        [$user, $companyA] = $this->createLicensedTenantUser();
        $companyB = Company::factory()->create();

        $condominiumA = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condominio Alfa',
            'status' => 'active',
            'slug' => 'condominio-alfa',
        ]);

        $condominiumB = Condominium::query()->create([
            'company_id' => $companyB->id,
            'name' => 'Condominio Beta',
            'status' => 'active',
            'slug' => 'condominio-beta',
        ]);

        Issue::query()->create([
            'company_id' => $companyA->id,
            'condominium_id' => $condominiumA->id,
            'subject' => 'Chamado correto',
            'description' => 'Pertence ao tenant ativo.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        $issueFromOtherCompany = Issue::query()->withoutGlobalScopes()->create([
            'company_id' => $companyB->id,
            'condominium_id' => $condominiumB->id,
            'subject' => 'Chamado indevido',
            'description' => 'Nao deve abrir por URL direta.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $companyA->id])
            ->get("/app/issues/{$issueFromOtherCompany->id}/edit")
            ->assertNotFound();
    }

    public function test_user_with_condominium_assignments_only_sees_assigned_issues(): void
    {
        [$user, $company] = $this->createLicensedTenantUser();
        $companyUser = $user->activeCompanyUserFor($company);

        $condominiumA = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condominio Acesso',
            'status' => 'active',
            'slug' => 'condominio-acesso',
        ]);

        $condominiumB = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condominio Bloqueado',
            'status' => 'active',
            'slug' => 'condominio-bloqueado',
        ]);

        $companyUser->condominiums()->attach($condominiumA->id);

        Issue::query()->create([
            'company_id' => $company->id,
            'condominium_id' => $condominiumA->id,
            'subject' => 'Chamado autorizado',
            'description' => 'Pode aparecer.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        Issue::query()->create([
            'company_id' => $company->id,
            'condominium_id' => $condominiumB->id,
            'subject' => 'Chamado bloqueado',
            'description' => 'Nao pode aparecer.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/issues')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/Issues/Index')
                ->has('issues.data', 1)
                ->where('issues.data.0.subject', 'Chamado autorizado'));
    }

    public function test_user_cannot_create_issue_for_unassigned_condominium(): void
    {
        [$user, $company] = $this->createLicensedTenantUser();
        $companyUser = $user->activeCompanyUserFor($company);

        $condominiumA = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condominio Acesso',
            'status' => 'active',
            'slug' => 'condominio-acesso',
        ]);

        $condominiumB = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condominio Bloqueado',
            'status' => 'active',
            'slug' => 'condominio-bloqueado',
        ]);

        $companyUser->condominiums()->attach($condominiumA->id);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->from('/app/issues/create')
            ->post('/app/issues', [
                'condominium_id' => $condominiumB->id,
                'subject' => 'Tentativa indevida',
                'description' => 'Nao deveria passar.',
                'status' => 'pendente',
                'priority' => 'media',
                'shared_with_residents' => false,
            ])
            ->assertRedirect('/app/issues/create')
            ->assertSessionHasErrors('condominium_id');

        $this->assertDatabaseCount('issues', 0);
    }

    private function createLicensedTenantUser(array $moduleKeys = ['chamados']): array
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $license = License::query()->create([
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
        ]);

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

        return [$user, $company];
    }
}
