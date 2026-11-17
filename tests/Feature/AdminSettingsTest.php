<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Top-bar profile menu > Settings: an admin edits their own account. */
class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['email' => 'staff@example.com', 'password' => Hash::make('Old-password1!')]);
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        return $admin;
    }

    public function test_any_admin_can_open_settings_and_sees_the_profile_menu(): void
    {
        $html = $this->actingAs($this->admin())->get('/settings')->assertOk()->getContent();

        $this->assertStringContainsString('id="profileMenu"', $html);
        $this->assertStringContainsString('id="profileForm"', $html);
    }

    public function test_admin_can_change_their_name_without_a_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patchJson('/settings/profile', ['name' => 'New Name', 'email' => 'staff@example.com'])
            ->assertOk();

        $this->assertSame('New Name', $admin->fresh()->name);
    }

    public function test_changing_email_needs_the_current_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patchJson('/settings/profile', ['name' => $admin->name, 'email' => 'new@example.com'])
            ->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->actingAs($admin)
            ->patchJson('/settings/profile', ['name' => $admin->name, 'email' => 'new@example.com', 'current_password' => 'wrong'])
            ->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->actingAs($admin)
            ->patchJson('/settings/profile', ['name' => $admin->name, 'email' => 'new@example.com', 'current_password' => 'Old-password1!'])
            ->assertOk();

        $this->assertSame('new@example.com', $admin->fresh()->email);
    }

    public function test_admin_can_upload_replace_and_remove_a_profile_picture(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/settings/avatar', ['avatar' => \Illuminate\Http\UploadedFile::fake()->image('me.jpg', 300, 300)], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonStructure(['avatar_url']);
        $first = $admin->fresh()->avatar_path;
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($first);

        // Shown in the top-bar menu.
        $this->assertStringContainsString(basename($first), $this->actingAs($admin)->get('/settings')->getContent());

        // Replacing deletes the old file.
        $this->actingAs($admin)
            ->post('/settings/avatar', ['avatar' => \Illuminate\Http\UploadedFile::fake()->image('me2.png')], ['Accept' => 'application/json'])
            ->assertOk();
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($first);

        $second = $admin->fresh()->avatar_path;
        $this->actingAs($admin)->deleteJson('/settings/avatar')->assertOk();
        $this->assertNull($admin->fresh()->avatar_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($second);
    }

    public function test_profile_picture_must_be_a_small_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/settings/avatar', ['avatar' => \Illuminate\Http\UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonValidationErrors('avatar');

        $this->actingAs($admin)
            ->post('/settings/avatar', ['avatar' => \Illuminate\Http\UploadedFile::fake()->image('big.jpg')->size(3000)], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonValidationErrors('avatar');
    }

    public function test_email_must_not_belong_to_someone_else(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patchJson('/settings/profile', ['name' => $admin->name, 'email' => 'taken@example.com', 'current_password' => 'Old-password1!'])
            ->assertStatus(422)->assertJsonValidationErrors('email');
    }
}
