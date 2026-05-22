<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_company_with_primary_admin_master(): void
    {
        $superadmin = User::factory()->create([
            'is_superadmin' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($superadmin)
            ->post('/superadmin/companies', [
                'name' => 'Andressa Gestao Condominial',
                'document' => '12.345.678/0001-90',
                'email' => 'contato@andressa.test',
                'phone' => '(31) 99999-0000',
                'responsible_name' => '',
                'slug' => 'andressa-gestao',
                'primary_color' => '#123456',
                'secondary_color' => '#654321',
                'status' => 'active',
                'primary_user_name' => 'Andressa Silva',
                'primary_user_email' => 'andressa@empresa.test',
                'primary_user_phone' => '(31) 98888-0000',
                'primary_user_password' => 'SenhaInicial123',
                'primary_user_password_confirmation' => 'SenhaInicial123',
                'primary_user_force_password_reset' => true,
            ])
            ->assertRedirect('/superadmin/companies');

        $company = Company::query()->where('slug', 'andressa-gestao')->firstOrFail();
        $company->load('primaryCompanyUser.user');

        $this->assertSame('Andressa Silva', $company->responsible_name);
        $this->assertNotNull($company->primaryCompanyUser);
        $this->assertTrue($company->primaryCompanyUser->is_primary);
        $this->assertSame('admin', $company->primaryCompanyUser->role);
        $this->assertSame('active', $company->primaryCompanyUser->status);
        $this->assertSame('andressa@empresa.test', $company->primaryCompanyUser->user->email);
        $this->assertTrue($company->primaryCompanyUser->user->must_change_password);

        $this->assertDatabaseHas('company_users', [
            'company_id' => $company->id,
            'user_id' => $company->primaryCompanyUser->user_id,
            'role' => 'admin',
            'status' => 'active',
            'is_primary' => true,
        ]);
    }
}
