<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOwnerCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_owner_with_every_privilege_on_an_empty_install(): void
    {
        $this->artisan('nestph:create-owner')
            ->expectsQuestion("Owner's full name", 'Maria Santos')
            ->expectsQuestion("Owner's email (used to log in)", 'maria@dorm.test')
            ->expectsQuestion('Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)', 'short')
            ->expectsOutput('The password field must be at least 8 characters.')
            ->expectsQuestion('Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)', 'Secret-pass-1')
            ->expectsQuestion('Type the password again', 'Secret-pass-1')
            ->assertSuccessful();

        $owner = User::where('email', 'maria@dorm.test')->firstOrFail();

        $this->assertSame('admin', $owner->role->role_name);
        $this->assertTrue((bool) $owner->is_active);
        $this->assertEqualsCanonicalizing(
            ['manage_tenants', 'manage_rooms', 'manage_contracts', 'manage_billing', 'manage_users', 'view_reports'],
            $owner->privileges->pluck('privilege_name')->all()
        );

        // The new Owner can actually sign in through the admin login.
        $this->postJson('/admin/login', ['email' => 'maria@dorm.test', 'password' => 'Secret-pass-1'])
            ->assertOk();
    }

    public function test_it_asks_before_creating_a_second_owner(): void
    {
        $this->artisan('nestph:create-owner')
            ->expectsQuestion("Owner's full name", 'First Owner')
            ->expectsQuestion("Owner's email (used to log in)", 'first@dorm.test')
            ->expectsQuestion('Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)', 'Secret-pass-1')
            ->expectsQuestion('Type the password again', 'Secret-pass-1')
            ->assertSuccessful();

        $this->artisan('nestph:create-owner')
            ->expectsConfirmation('Create another Owner account anyway?', 'no')
            ->expectsOutput('Nothing was created.')
            ->assertSuccessful();

        $this->assertSame(1, User::count());
    }
}
