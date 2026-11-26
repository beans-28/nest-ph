<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Pages fetched ahead on hover (public/js/smooth-nav.js) must not change what the person sees next. */
class PrefetchSafetyTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $owner = User::factory()->create();
        $owner->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        AdminPrivilege::create(['user_id' => $owner->id, 'privilege_name' => 'manage_users']);

        return $owner;
    }

    public function test_a_prefetch_keeps_one_time_flash_messages_for_the_next_real_page(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->withSession(['status' => 'Saved.'])
            ->get('/payments', ['Sec-Purpose' => 'prefetch'])->assertOk();

        // The message is still there for the page the person actually opens.
        $this->assertSame('Saved.', session('status'));
    }

    public function test_logged_in_pages_are_not_kept_for_the_back_button(): void
    {
        $this->actingAs($this->owner())->get('/dashboard')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_every_page_opts_in_to_the_fade_before_anything_else_in_head(): void
    {
        $html = $this->actingAs($this->owner())->get('/dashboard')->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '<link'));

        $this->assertStringContainsString('@view-transition{navigation:auto}', $head);
        // Reduced-motion users keep the crossfade (opacity only, no movement).
        $this->assertStringNotContainsString('navigation:none', $head);
        $this->assertStringContainsString('/css/motion.css', $html);
    }
}
