<?php

namespace Tests\Feature;

use App\Models\PartnerRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => Role::firstOrCreate(['role_name' => $role])->id,
            'is_active' => true,
        ]);
    }

    private function makeRequest(): PartnerRequest
    {
        return PartnerRequest::create([
            'owner_name' => 'Juan Dela Cruz',
            'dorm_name' => 'Sampaloc Haven',
            'email' => 'juan@example.com',
            'contact_number' => '09171234567',
            'dpa_consent' => true,
        ]);
    }

    public function test_platform_admin_sees_owner_requests(): void
    {
        $this->makeRequest();

        $this->actingAs($this->userWithRole('platform_admin'))
            ->get('/platform')
            ->assertOk()
            ->assertSee('Sampaloc Haven');
    }

    public function test_dorm_admin_and_tenant_cannot_open_platform_dashboard(): void
    {
        $this->actingAs($this->userWithRole('admin'))->get('/platform')->assertForbidden();
        $this->actingAs($this->userWithRole('tenant'))->get('/platform')->assertForbidden();
    }

    public function test_guest_is_sent_to_platform_login(): void
    {
        $this->get('/platform')->assertRedirect(route('login.platform'));
    }

    public function test_platform_admin_cannot_open_dorm_admin_pages(): void
    {
        $this->actingAs($this->userWithRole('platform_admin'))
            ->get(route('admin.addfloor'))
            ->assertForbidden();
    }

    public function test_platform_admin_updates_status_and_notes(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->userWithRole('platform_admin'))
            ->patch("/platform/requests/{$req->id}", ['status' => 'contacted', 'notes' => 'Called owner'])
            ->assertRedirect();

        $req->refresh();
        $this->assertSame('contacted', $req->status);
        $this->assertSame('Called owner', $req->notes);
    }

    public function test_each_login_page_only_accepts_its_own_role(): void
    {
        $platform = $this->userWithRole('platform_admin');
        $admin = $this->userWithRole('admin');

        $this->postJson('/platform/login', ['email' => $platform->email, 'password' => 'password123'])->assertOk();
        $this->post('/logout');

        $this->postJson('/admin/login', ['email' => $platform->email, 'password' => 'password123'])->assertForbidden();
        $this->postJson('/tenant/login', ['email' => $platform->email, 'password' => 'password123'])->assertForbidden();
        $this->postJson('/platform/login', ['email' => $admin->email, 'password' => 'password123'])->assertForbidden();
    }
}
