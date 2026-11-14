<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards against the Nov 2026 production 500: a Blade syntax error in
 * admindashboard.blade.php broke /dashboard for every admin account.
 */
class AdminDashboardRendersTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_renders(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        AdminPrivilege::create(['user_id' => $owner->id, 'privilege_name' => 'manage_users']);

        $this->actingAs($owner)->get('/dashboard')->assertOk();
    }

    public function test_limited_admin_dashboard_renders(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }
}
