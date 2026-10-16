<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\MonthlyExpense;
use App\Models\Role;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Panel revisions: room area (m²) and per-bed vs whole-room pricing on
 * listings, plus the forecast's comparison lines, utility range and
 * contract-locked rent.
 */
class RoomListingAndForecastExtrasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        AdminPrivilege::create(['user_id' => $admin->id, 'privilege_name' => 'view_reports']);

        return $admin;
    }

    public function test_room_area_and_pricing_mode_reach_the_public_listing(): void
    {
        $type = RoomType::create(['name' => 'Solo room', 'pricing_mode' => 'per_room', 'monthly_rate' => 9000]);

        $this->actingAs($this->admin())->postJson('/vacancy/rooms', [
            'room_no' => '901', 'floor' => '9', 'room_type_id' => $type->id,
            'area_sqm' => 18.5, 'bed_count' => 2,
        ])->assertSuccessful()->assertJsonPath('area_sqm', 18.5)->assertJsonPath('pricing_mode', 'per_room');

        $room = $this->findRoom($this->getJson('/public-api/rooms')->assertOk()->json(), '901');

        $this->assertNotNull($room);
        $this->assertEquals(18.5, $room['area_sqm']);
        $this->assertSame('per_room', $room['pricing_mode']);
        $this->assertEquals(4500, $room['price_per_bed']);
        $this->assertSame(2, $room['bed_count']);
    }

    /** Finds a room by number anywhere in the listing (rooms may be grouped). */
    private function findRoom($data, string $no): ?array
    {
        if (! is_array($data)) {
            return null;
        }
        if (($data['room_no'] ?? null) === $no) {
            return $data;
        }
        foreach ($data as $value) {
            if ($found = $this->findRoom($value, $no)) {
                return $found;
            }
        }

        return null;
    }

    public function test_room_area_is_optional_and_must_be_a_positive_number(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/vacancy/rooms', ['room_no' => '902', 'floor' => '9', 'bed_count' => 1])
            ->assertSuccessful()->assertJsonPath('area_sqm', null);

        $this->actingAs($admin)->postJson('/vacancy/rooms', ['room_no' => '903', 'floor' => '9', 'bed_count' => 1, 'area_sqm' => -5])
            ->assertStatus(422);
    }

    public function test_forecast_shows_utility_range_and_uses_same_month_last_year(): void
    {
        foreach ([1 => 10000, 2 => 20000, 3 => 15000] as $back => $electricity) {
            MonthlyExpense::create(['month' => now()->startOfMonth()->subMonthsNoOverflow($back)->toDateString(),
                'electricity' => $electricity, 'water' => 0, 'internet' => 0, 'salaries' => 5000, 'other' => 0]);
        }
        // Next month, a year ago: a hot month with a big bill.
        MonthlyExpense::create(['month' => now()->startOfMonth()->addMonthNoOverflow()->subYear()->toDateString(),
            'electricity' => 40000, 'water' => 0, 'internet' => 0, 'salaries' => 5000, 'other' => 0]);

        $res = $this->actingAs($this->admin())->getJson('/reports/forecast')->assertOk();

        $this->assertEquals(10000, $res->json('assumptions.min_utilities'));
        $this->assertEquals(20000, $res->json('assumptions.max_utilities'));
        $next = $res->json('forecast.0');
        $this->assertTrue($next['seasonal']);
        // steady 5,000 + average of (15,000 six-month avg, 40,000 last year)
        $this->assertEquals(32500, $next['expenses']);
        $this->assertEquals(15000, $next['expenses_low']);
        $this->assertEquals(45000, $next['expenses_high']);
        $this->assertArrayHasKey('locked_beds', $next);
    }

    public function test_past_months_carry_the_forecast_made_at_the_time(): void
    {
        $res = $this->actingAs($this->admin())->getJson('/reports/forecast')->assertOk();

        foreach ($res->json('history') as $row) {
            $this->assertArrayHasKey('forecast_income', $row);
            $this->assertArrayHasKey('forecast_expenses', $row);
        }
        // The current month isn't over, so it has nothing to compare.
        $this->assertNull($res->json('history.5.forecast_income'));
    }
}
