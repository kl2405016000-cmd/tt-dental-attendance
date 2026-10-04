<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_login_page_renders_staff_portal_without_admin_login_option(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Staff');
        $response->assertDontSee('Admin Login');
    }

    public function test_legacy_login_urls_redirect_to_single_login_page(): void
    {
        $this->get('/admin/login')->assertRedirect('/login');
        $this->get('/staff/login')->assertRedirect('/login');
    }

    public function test_admin_credentials_on_single_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_staff_credentials_on_single_login_redirects_to_staff_dashboard(): void
    {
        $staff = User::factory()->create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'staff',
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $this->assertAuthenticatedAs($staff);
    }

    public function test_authenticated_admin_visiting_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/login')
            ->assertRedirect('/admin/dashboard');
    }

    public function test_authenticated_staff_visiting_login_redirects_to_staff_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get('/login')
            ->assertRedirect('/staff/dashboard');
    }

    public function test_staff_user_cannot_access_any_admin_routes(): void
    {
        $staff = User::factory()->create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'role' => 'staff',
        ]);

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/employees',
            '/admin/attendance',
            '/admin/biometric',
            '/admin/face-registration',
            '/admin/mc',
            '/admin/absences',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($staff)->get($route);
            $response->assertRedirect('/staff/dashboard');
            $response->assertSessionHas('error');
        }
    }

    public function test_admin_user_cannot_access_staff_dashboard_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/staff/dashboard');
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_root_url_redirects_guest_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_logout_redirects_to_single_login_page(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionHas('success');
    }

    public function test_staff_can_access_attendance_history_and_mc_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get('/staff/history')
            ->assertStatus(200)
            ->assertSee('My Attendance History')
            ->assertSee('Daily Attendance Records');

        $this->actingAs($staff)
            ->get('/staff/mc')
            ->assertStatus(200)
            ->assertSee('Medical Certificates')
            ->assertSee('Submit Medical Certificate');
    }
}
