<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                ->where('currentVersion.number', '0.2.0')
                ->where('currentVersion.name', 'Web Foundation')
                ->where('currentVersion.visibility', 'superadmin'));
    }

    public function test_regular_user_login_sets_current_company_in_session(): void
    {
        $user = User::factory()->create([
            'email' => 'tenant@example.com',
            'password' => 'password',
        ]);

        $company = Company::factory()->create();

        $user->companies()->attach($company->id, [
            'role' => 'admin',
            'status' => 'active',
            'can_access_whatsapp' => false,
            'only_responsible_issues' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'tenant@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('current_company_id', $company->id);
        $this->assertAuthenticatedAs($user);
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
}
