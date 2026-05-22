<?php

namespace Tests\Feature\Permissions;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Condominium;
use App\Models\Issue;
use App\Models\License;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CompanyUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_internal_user_with_condominium_scope_and_audit_log(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin');

        $condominiumA = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Centro',
            'status' => 'active',
            'slug' => 'condominio-centro',
        ]);

        $condominiumB = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Parque',
            'status' => 'active',
            'slug' => 'condominio-parque',
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->post('/app/users', [
                'name' => 'Maria Operacional',
                'email' => 'maria.operacional@example.com',
                'phone' => '11999990000',
                'password' => 'segredo123',
                'password_confirmation' => 'segredo123',
                'role' => 'operacional',
                'status' => 'active',
                'can_access_whatsapp' => true,
                'only_responsible_issues' => true,
                'condominium_ids' => [$condominiumA->id, $condominiumB->id],
            ])
            ->assertRedirect('/app/users');

        $user = User::query()->where('email', 'maria.operacional@example.com')->firstOrFail();
        $companyUser = CompanyUser::query()->where('company_id', $company->id)->where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('company_users', [
            'id' => $companyUser->id,
            'role' => 'operacional',
            'status' => 'active',
            'can_access_whatsapp' => true,
            'only_responsible_issues' => true,
        ]);

        $this->assertDatabaseHas('user_condominiums', [
            'company_user_id' => $companyUser->id,
            'condominium_id' => $condominiumA->id,
        ]);

        $this->assertDatabaseHas('user_condominiums', [
            'company_user_id' => $companyUser->id,
            'condominium_id' => $condominiumB->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'action' => 'company_user.created',
            'auditable_type' => CompanyUser::class,
            'auditable_id' => $companyUser->id,
        ]);
    }

    public function test_admin_can_update_internal_user_and_register_audit_log(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin');

        $managedUser = User::factory()->create([
            'email' => 'operacional@example.com',
            'name' => 'Operacional Inicial',
        ]);

        $managedMembership = CompanyUser::query()->create([
            'company_id' => $company->id,
            'user_id' => $managedUser->id,
            'role' => 'operacional',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->put("/app/users/{$managedMembership->id}", [
                'name' => 'Operacional Atualizado',
                'email' => 'operacional@example.com',
                'phone' => '11912345678',
                'role' => 'gestor',
                'status' => 'active',
                'can_access_whatsapp' => true,
                'only_responsible_issues' => true,
                'condominium_ids' => [],
            ])
            ->assertRedirect('/app/users');

        $this->assertDatabaseHas('company_users', [
            'id' => $managedMembership->id,
            'role' => 'gestor',
            'can_access_whatsapp' => true,
            'only_responsible_issues' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'action' => 'company_user.updated',
            'auditable_type' => CompanyUser::class,
            'auditable_id' => $managedMembership->id,
        ]);
    }

    public function test_existing_user_can_be_linked_without_redefining_password(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin');
        $existingUser = User::factory()->create([
            'email' => 'usuario.existente@example.com',
            'name' => 'Usuário Existente',
        ]);

        $this->assertDatabaseCount('users', 2);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->post('/app/users', [
                'name' => 'Usuário Existente',
                'email' => 'usuario.existente@example.com',
                'phone' => '11900001111',
                'role' => 'gestor',
                'status' => 'active',
                'can_access_whatsapp' => false,
                'only_responsible_issues' => false,
                'condominium_ids' => [],
            ])
            ->assertRedirect('/app/users');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('company_users', [
            'company_id' => $company->id,
            'user_id' => $existingUser->id,
            'role' => 'gestor',
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_company_user_management(): void
    {
        [$gestor, $company] = $this->createTenantUserWithCompany(role: 'gestor');

        $this->actingAs($gestor)
            ->withSession(['current_company_id' => $company->id])
            ->get('/app/users')
            ->assertForbidden();
    }

    public function test_license_limit_blocks_creation_of_new_active_internal_user(): void
    {
        [$admin, $company] = $this->createTenantUserWithCompany(role: 'admin', maxInternalUsers: 1);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->from('/app/users/create')
            ->post('/app/users', [
                'name' => 'Novo Usuário',
                'email' => 'novo.usuario@example.com',
                'phone' => null,
                'password' => 'segredo123',
                'password_confirmation' => 'segredo123',
                'role' => 'financeiro',
                'status' => 'active',
                'can_access_whatsapp' => false,
                'only_responsible_issues' => false,
                'condominium_ids' => [],
            ])
            ->assertRedirect('/app/users/create')
            ->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('users', [
            'email' => 'novo.usuario@example.com',
        ]);
    }

    public function test_last_active_admin_cannot_be_inactivated(): void
    {
        [$admin, $company, $companyUser] = $this->createTenantUserWithCompany(role: 'admin', returnMembership: true);

        $this->actingAs($admin)
            ->withSession(['current_company_id' => $company->id])
            ->from('/app/users')
            ->delete("/app/users/{$companyUser->id}")
            ->assertRedirect('/app/users')
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('company_users', [
            'id' => $companyUser->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseMissing('audit_logs', [
            'action' => 'company_user.deactivated',
            'auditable_id' => $companyUser->id,
        ]);
    }

    public function test_operational_user_limited_to_assigned_issues_sees_only_own_queue(): void
    {
        [$user, $company] = $this->createTenantUserWithCompany(
            role: 'operacional',
            moduleKeys: ['chamados'],
            onlyResponsibleIssues: true,
        );

        $condominium = Condominium::query()->create([
            'company_id' => $company->id,
            'name' => 'Condomínio Operacional',
            'status' => 'active',
            'slug' => 'condominio-operacional',
        ]);

        Issue::query()->create([
            'company_id' => $company->id,
            'condominium_id' => $condominium->id,
            'responsible_user_id' => $user->id,
            'subject' => 'Chamado atribuído',
            'description' => 'Deve aparecer para o usuário operacional.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        Issue::query()->create([
            'company_id' => $company->id,
            'condominium_id' => $condominium->id,
            'responsible_user_id' => null,
            'subject' => 'Chamado sem responsável',
            'description' => 'Não deve aparecer para quem só vê chamados atribuídos.',
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
                ->where('issues.data.0.subject', 'Chamado atribuído'));
    }

    private function createTenantUserWithCompany(
        string $role = 'admin',
        int $maxInternalUsers = 10,
        bool $returnMembership = false,
        array $moduleKeys = ['configuracoes'],
        bool $onlyResponsibleIssues = false,
    ): array {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $companyUser = CompanyUser::query()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'role' => $role,
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => $onlyResponsibleIssues,
        ]);

        $license = License::query()->create([
            'company_id' => $company->id,
            'contract_number' => 'CTR-USERS-001',
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
            'max_internal_users' => $maxInternalUsers,
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
                'category' => 'Operacional',
                'active' => true,
            ]);

            $license->modules()->attach($module->id, ['enabled' => true]);
        }

        return $returnMembership ? [$user, $company, $companyUser] : [$user, $company];
    }
}
