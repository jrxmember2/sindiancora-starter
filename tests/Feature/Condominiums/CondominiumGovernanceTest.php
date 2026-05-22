<?php

namespace Tests\Feature\Condominiums;

use App\Models\Company;
use App\Models\Condominium;
use App\Models\CondominiumLinkRequest;
use App\Models\Document;
use App\Models\Issue;
use App\Models\License;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CondominiumGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_condominium_document_opens_a_link_request_instead_of_creating_a_new_record(): void
    {
        [$userA, $companyA] = $this->createLicensedTenantAdmin('Gestão Andressa');
        [$userB, $companyB] = $this->createLicensedTenantAdmin('Gestão Márcia');

        $condominium = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condomínio Aurora',
            'document' => '12.345.678/0001-90',
            'status' => 'active',
            'slug' => 'condominio-aurora',
        ]);

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->post('/app/condominiums', [
                'name' => 'Condomínio Aurora',
                'document' => '12.345.678/0001-90',
                'status' => 'active',
                'slug' => 'condominio-aurora-marcia',
            ])
            ->assertRedirect('/app/condominium-link-requests');

        $this->assertDatabaseCount('condominiums', 1);
        $this->assertDatabaseHas('condominium_link_requests', [
            'condominium_id' => $condominium->id,
            'requesting_company_id' => $companyB->id,
            'current_primary_company_id' => $companyA->id,
            'status' => 'pending',
        ]);
    }

    public function test_share_keeps_master_registry_with_principal_company_and_grants_operational_access_to_the_solidary_company(): void
    {
        [$userA, $companyA] = $this->createLicensedTenantAdmin('Gestão Andressa');
        [$userB, $companyB] = $this->createLicensedTenantAdmin('Gestão Márcia');

        $condominium = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condomínio Aurora',
            'document' => '12.345.678/0001-90',
            'status' => 'active',
            'slug' => 'condominio-aurora',
        ]);

        Issue::query()->withoutGlobalScopes()->create([
            'company_id' => $companyA->id,
            'condominium_id' => $condominium->id,
            'subject' => 'Chamado compartilhado',
            'description' => 'Deve aparecer para a empresa solidária.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        Document::query()->withoutGlobalScopes()->create([
            'company_id' => $companyA->id,
            'condominium_id' => $condominium->id,
            'title' => 'Ata compartilhada',
            'document_type' => 'ata',
            'status' => 'valido',
            'created_by' => $userA->id,
        ]);

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->post('/app/condominiums', [
                'name' => 'Condomínio Aurora',
                'document' => '12.345.678/0001-90',
                'status' => 'active',
                'slug' => 'condominio-aurora-marcia',
            ]);

        $linkRequest = CondominiumLinkRequest::query()->firstOrFail();

        $this->actingAs($userA)
            ->withSession(['current_company_id' => $companyA->id])
            ->post("/app/condominium-link-requests/{$linkRequest->id}/decide", [
                'decision' => 'share',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('company_condominiums', [
            'company_id' => $companyB->id,
            'condominium_id' => $condominium->id,
            'relationship_type' => 'solidaria',
            'status' => 'active',
        ]);

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->get('/app/issues')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/Issues/Index')
                ->has('issues.data', 1)
                ->where('issues.data.0.subject', 'Chamado compartilhado'));

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->get('/app/documents')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tenant/Documents/Index')
                ->has('items.data', 1)
                ->where('items.data.0.title', 'Ata compartilhada'));

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->get("/app/condominiums/{$condominium->id}/edit")
            ->assertForbidden();
    }

    public function test_transfer_moves_the_principal_link_and_revokes_access_from_the_previous_company(): void
    {
        [$userA, $companyA] = $this->createLicensedTenantAdmin('Gestão Andressa');
        [$userB, $companyB] = $this->createLicensedTenantAdmin('Gestão Márcia');

        $condominium = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condomínio Aurora',
            'document' => '12.345.678/0001-90',
            'status' => 'active',
            'slug' => 'condominio-aurora',
        ]);

        $issue = Issue::query()->withoutGlobalScopes()->create([
            'company_id' => $companyA->id,
            'condominium_id' => $condominium->id,
            'subject' => 'Chamado transferido',
            'description' => 'Deve trocar de domínio operacional.',
            'status' => 'pendente',
            'priority' => 'media',
            'origin' => 'interno',
            'opened_at' => now(),
        ]);

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->post('/app/condominiums', [
                'name' => 'Condomínio Aurora',
                'document' => '12.345.678/0001-90',
                'status' => 'active',
                'slug' => 'condominio-aurora-marcia',
            ]);

        $linkRequest = CondominiumLinkRequest::query()->firstOrFail();

        $this->actingAs($userA)
            ->withSession(['current_company_id' => $companyA->id])
            ->post("/app/condominium-link-requests/{$linkRequest->id}/decide", [
                'decision' => 'transfer',
            ])
            ->assertRedirect();

        $condominium->refresh();

        $this->assertSame($companyB->id, $condominium->company_id);
        $this->assertDatabaseHas('company_condominiums', [
            'company_id' => $companyA->id,
            'condominium_id' => $condominium->id,
            'relationship_type' => 'principal',
            'status' => 'transferred',
        ]);
        $this->assertDatabaseHas('company_condominiums', [
            'company_id' => $companyB->id,
            'condominium_id' => $condominium->id,
            'relationship_type' => 'principal',
            'status' => 'active',
        ]);

        $this->actingAs($userA)
            ->withSession(['current_company_id' => $companyA->id])
            ->get("/app/issues/{$issue->id}/edit")
            ->assertNotFound();

        $this->actingAs($userB)
            ->withSession(['current_company_id' => $companyB->id])
            ->get("/app/issues/{$issue->id}/edit")
            ->assertOk();
    }

    public function test_superadmin_can_force_a_transfer_even_without_available_license_capacity(): void
    {
        $superadmin = User::factory()->create([
            'is_superadmin' => true,
            'must_change_password' => false,
        ]);

        [$userA, $companyA] = $this->createLicensedTenantAdmin('Gestão Andressa');
        [, $companyB] = $this->createLicensedTenantAdmin('Gestão Márcia', maxCondominiums: 0);

        $condominium = Condominium::query()->create([
            'company_id' => $companyA->id,
            'name' => 'Condomínio Aurora',
            'document' => '12.345.678/0001-90',
            'status' => 'active',
            'slug' => 'condominio-aurora',
        ]);

        $this->actingAs($superadmin)
            ->post("/superadmin/condominium-governance/{$condominium->id}/force-transfer", [
                'target_company_id' => $companyB->id,
                'decision_notes' => 'Ajuste contratual mediado pela plataforma.',
            ])
            ->assertRedirect();

        $condominium->refresh();

        $this->assertSame($companyB->id, $condominium->company_id);
        $this->assertDatabaseHas('condominium_link_requests', [
            'condominium_id' => $condominium->id,
            'requesting_company_id' => $companyB->id,
            'status' => 'transferred',
            'decision_type' => 'transferir',
        ]);

        $this->actingAs($userA)
            ->withSession(['current_company_id' => $companyA->id])
            ->get("/app/condominiums/{$condominium->id}/edit")
            ->assertNotFound();
    }

    private function createLicensedTenantAdmin(
        string $companyName,
        array $moduleKeys = ['configuracoes', 'chamados', 'documentos'],
        int $maxCondominiums = 10,
    ): array {
        $user = User::factory()->create([
            'must_change_password' => false,
        ]);

        $company = Company::factory()->create([
            'name' => $companyName,
            'status' => 'active',
        ]);

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
            'is_primary' => true,
        ]);

        $license = License::query()->create([
            'company_id' => $company->id,
            'contract_number' => 'CTR-'.str_pad((string) $company->id, 3, '0', STR_PAD_LEFT),
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
            $module = Module::query()->firstOrCreate(
                ['key' => $moduleKey],
                [
                    'name' => ucfirst(str_replace('_', ' ', $moduleKey)),
                    'description' => "Módulo {$moduleKey}",
                    'category' => 'Operacional',
                    'active' => true,
                ]
            );

            $license->modules()->attach($module->id, ['enabled' => true]);
        }

        return [$user, $company, $license];
    }
}
