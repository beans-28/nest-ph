<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tenants change their own profile picture (2x2 ID picture) from My Profile.
 */
class TenantProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    private function tenantUser(): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'tenant'])->id])->save();
        $tenant = Tenant::create([
            'first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana' . random_int(1, 99999) . '@test.ph',
            'contact_number' => '09170000000', 'status' => 'active', 'user_id' => $user->id,
        ]);

        return [$user, $tenant];
    }

    public function test_tenant_can_upload_a_new_profile_picture(): void
    {
        Storage::fake('public');
        [$user, $tenant] = $this->tenantUser();

        $this->actingAs($user)
            ->post('/account/photo', ['photo' => UploadedFile::fake()->image('me.jpg', 600, 600)], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonStructure(['message', 'url']);

        $path = $tenant->fresh()->id_photo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_non_images_are_rejected(): void
    {
        Storage::fake('public');
        [$user, $tenant] = $this->tenantUser();

        $this->actingAs($user)
            ->post('/account/photo', ['photo' => UploadedFile::fake()->create('notes.pdf', 50, 'application/pdf')], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertNull($tenant->fresh()->id_photo_path);
    }

    public function test_profile_page_shows_the_change_picture_button(): void
    {
        [$user] = $this->tenantUser();

        $this->actingAs($user)->get('/account')
            ->assertOk()
            ->assertSee('Change your profile picture')
            ->assertSee('Crop your picture');
    }
}
