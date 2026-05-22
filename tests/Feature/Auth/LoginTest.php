<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_available(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
    }

    public function test_superadmin_can_authenticate_and_access_versions_page(): void
    {
        $user = User::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'is_superadmin' => true,
            'must_change_password' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->get('/superadmin/versions')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('SuperAdmin/Versions/Index')
                ->where('currentVersion.number', '0.7.0')
                ->where('currentVersion.name', 'Tenant Governance')
                ->where('currentVersion.visibility', 'superadmin'));
    }

    public function test_regular_user_login_sets_current_company_in_session(): void
    {
        $user = User::factory()->create([
            'email' => 'tenant@example.com',
            'password' => 'password',
            'must_change_password' => false,
        ]);

        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
            'is_primary' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'tenant@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('current_company_id', $company->id);
        $this->assertAuthenticatedAs($user);
    }

    public function test_first_access_user_is_redirected_to_password_setup(): void
    {
        $user = User::factory()->create([
            'email' => 'first-access@example.com',
            'password' => 'password',
            'must_change_password' => true,
        ]);

        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
            'is_primary' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'first-access@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/primeiro-acesso');
        $response->assertSessionHas('current_company_id', $company->id);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_finish_first_access_password_setup(): void
    {
        $user = User::factory()->create([
            'must_change_password' => true,
        ]);

        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
            'is_primary' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->put('/primeiro-acesso', [
                'password' => 'NovaSenha123',
                'password_confirmation' => 'NovaSenha123',
            ])
            ->assertRedirect('/dashboard');

        $user->refresh();

        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('NovaSenha123', $user->password));

        $this->actingAs($user)
            ->withSession(['current_company_id' => $company->id])
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_invalid_credentials_do_not_authenticate_the_user(): void
    {
        User::factory()->create([
            'email' => 'invalid@example.com',
            'password' => 'password',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_without_active_company_membership_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive-membership@example.com',
            'password' => 'password',
        ]);

        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'operacional',
            'status' => 'inactive',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
            'is_primary' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'inactive-membership@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
