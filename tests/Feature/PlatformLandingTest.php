<?php

namespace Tests\Feature;

use App\Models\PartnerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_shows_generic_nest_ph_landing_with_dorm_choice(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Choose a dormitory')
            ->assertSee('Request NEST.PH for your dormitory')
            ->assertSee(route('home'));
    }

    public function test_dorm_homepage_moved_to_its_own_url(): void
    {
        $this->get('/pureza-station')->assertOk();
    }

    public function test_owner_can_request_nest_ph(): void
    {
        $this->postJson('/partner-requests', [
            'owner_name' => 'Juan Dela Cruz',
            'dorm_name' => 'Sampaloc Haven',
            'email' => 'juan@example.com',
            'contact_number' => '09171234567',
            'bed_count' => 40,
            'dpa_consent' => 1,
        ])->assertOk();

        $this->assertSame('Sampaloc Haven', PartnerRequest::first()->dorm_name);
    }

    public function test_request_needs_consent_and_required_fields(): void
    {
        $this->postJson('/partner-requests', ['owner_name' => 'Juan'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dorm_name', 'email', 'contact_number', 'dpa_consent']);
    }
}
