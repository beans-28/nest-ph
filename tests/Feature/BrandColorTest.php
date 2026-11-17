<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\DormitoryProfile;
use App\Models\Role;
use App\Models\User;
use App\Support\BrandTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Dormitory Profile > Site Color: one picked color re-themes every page. */
class BrandColorTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $owner = User::factory()->create();
        $owner->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        AdminPrivilege::create(['user_id' => $owner->id, 'privilege_name' => 'manage_users']);

        return $owner;
    }

    public function test_owner_can_save_a_color_and_pages_use_it(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->postJson('/dormitory-profile/brand-color', ['brand_color' => '#C98A2B'])
            ->assertOk();

        $this->assertSame('#c98a2b', DormitoryProfile::current()->brand_color);

        $html = $this->actingAs($owner)->get('/dashboard')->assertOk()->getContent();
        $this->assertStringContainsString('<style id="brand-theme">', $html);
        $this->assertStringContainsString('--green-accent:', $html);
        // Lands inside <head>, after the page's own styles.
        $this->assertLessThan(strpos($html, '</head>'), strpos($html, 'brand-theme'));
    }

    public function test_default_green_adds_nothing(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->postJson('/dormitory-profile/brand-color', ['brand_color' => BrandTheme::DEFAULT])
            ->assertOk();

        $this->assertNull(DormitoryProfile::current()->brand_color);
        $this->assertStringNotContainsString('brand-theme', $this->actingAs($owner)->get('/dashboard')->getContent());
    }

    public function test_rejects_anything_that_is_not_a_hex_color(): void
    {
        $this->actingAs($this->owner())
            ->postJson('/dormitory-profile/brand-color', ['brand_color' => 'red;}body{display:none'])
            ->assertStatus(422);
    }

    public function test_limited_admin_cannot_change_it(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        $this->actingAs($admin)
            ->postJson('/dormitory-profile/brand-color', ['brand_color' => '#c98a2b'])
            ->assertForbidden();
    }

    public function test_white_text_stays_readable_on_dark_shades_for_every_preset(): void
    {
        $lum = function (string $hex) {
            $lin = fn ($c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
            [$r, $g, $b] = array_map(fn ($c) => $lin(hexdec($c) / 255), str_split(substr($hex, 1), 2));

            return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        };
        foreach ([...BrandTheme::PRESETS, 'Yellow' => '#f2d21b'] as $hex) {
            $css = BrandTheme::styleTag($hex);
            foreach (['--green-sidebar-top', '--green-accent', '--green-btn', '--sage-700'] as $var) {
                if (! preg_match('/'.$var.':(#[0-9a-f]{6})/', $css, $m)) {
                    continue; // default green: no override
                }
                $this->assertGreaterThanOrEqual(3, 1.05 / ($lum($m[1]) + 0.05), "$var for $hex");
            }
        }
    }

    public function test_shades_keep_the_hue(): void
    {
        $css = BrandTheme::styleTag('#c98a2b');
        preg_match('/--green-accent:(#[0-9a-f]{6})/', $css, $m);
        // Gold at a darker lightness: red > green > blue, readable on white.
        [$r, $g, $b] = array_map('hexdec', str_split(substr($m[1], 1), 2));
        $this->assertTrue($r > $g && $g > $b);
        $this->assertLessThan(170, $r);
    }

    public function test_hardcoded_greens_on_login_pages_are_recolored(): void
    {
        $html = BrandTheme::recolorHtml('background: linear-gradient(#567357, #59473f); color:#197335', '#c98a2b');
        $this->assertStringNotContainsString('#567357', $html);
        $this->assertStringNotContainsString('#197335', $html);
        $this->assertStringContainsString('#59473f', $html); // brown is not a brand color
        $this->assertSame('#567357', BrandTheme::recolorHtml('#567357', BrandTheme::DEFAULT));
    }

    public function test_light_pick_shows_on_sidebar_with_dark_text(): void
    {
        $css = BrandTheme::styleTag('#fde68a');
        $this->assertStringContainsString('.sidebar{background:linear-gradient(180deg,#fde68a', $css);
        $this->assertStringContainsString('color:#292420', $css);
        // A normal-depth pick keeps the white-text sidebar.
        $this->assertStringNotContainsString('.sidebar{', BrandTheme::styleTag('#2f5d8c'));
    }

    public function test_owner_can_pick_a_light_background_and_pages_use_it(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->postJson('/dormitory-profile/background-color', ['background_color' => '#efe4d3'])
            ->assertOk();

        $this->assertSame('#efe4d3', DormitoryProfile::current()->background_color);
        $html = $this->actingAs($owner)->get('/dashboard')->getContent();
        $this->assertStringContainsString('<style id="brand-background">', $html);
        $this->assertStringContainsString('--bg-page:#efe4d3', $html);
    }

    public function test_background_rejects_anything_outside_the_light_presets(): void
    {
        foreach (['#000000', '#c98a2b', 'red'] as $bad) {
            $this->actingAs($this->owner())
                ->postJson('/dormitory-profile/background-color', ['background_color' => $bad])
                ->assertStatus(422);
        }
    }

    public function test_public_rooms_page_greens_follow_the_theme_but_status_dots_stay_green(): void
    {
        $html = file_get_contents(resource_path('views/publicrooms.blade.php'));
        $out = BrandTheme::recolorHtml($html, '#c98a2b');

        // Page background and card buttons are brand colors: swapped.
        $this->assertStringNotContainsString('#4e7454', $out);
        $this->assertStringNotContainsString('#1d4f27', $out);
        // The vacant-bed dot means "vacant": unchanged.
        $this->assertStringContainsString('#00d444', $out);
    }

    public function test_reset_links_show_only_when_a_color_is_set(): void
    {
        $owner = $this->owner();

        $html = $this->actingAs($owner)->get('/dormitory-profile')->assertOk()->getContent();
        $this->assertStringNotContainsString('id="siteColorReset"', $html);
        $this->assertStringNotContainsString('id="bgColorReset"', $html);

        $this->actingAs($owner)->postJson('/dormitory-profile/brand-color', ['brand_color' => '#c98a2b'])->assertOk();
        $this->actingAs($owner)->postJson('/dormitory-profile/background-color', ['background_color' => '#efe4d3'])->assertOk();

        $html = $this->actingAs($owner)->get('/dormitory-profile')->getContent();
        $this->assertStringContainsString('id="siteColorReset"', $html);
        $this->assertStringContainsString('id="bgColorReset"', $html);
    }
}
