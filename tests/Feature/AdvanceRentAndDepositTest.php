<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BillingController;
use App\Models\BillingStatement;
use App\Models\DepositRefund;
use App\Models\DormitoryProfile;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PurezaStationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Tool defense revision (billing): the dorm chooses how many months of
 * advance rent and security deposit it collects, the advance pays the
 * first months, the "included in rent" settings change the monthly total,
 * and the tenant can see where their deposit went.
 */
class AdvanceRentAndDepositTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PurezaStationSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** A 4-bed room whose ₱2,000 utilities and ₱400 Wi-Fi split to ₱500 and ₱100 a bed. */
    private function room(): Room
    {
        $type = RoomType::where('name', 'Room with AC, 4 persons')->where('location', '2nd to 5th floor')->firstOrFail();
        $floor = Floor::firstOrCreate(['floor_number' => 2], ['floor_name' => 'Floor 2']);
        $room = $floor->rooms()->create(['room_no' => 'R' . random_int(100, 999), 'room_type_id' => $type->id, 'status' => 'available']);
        for ($i = 1; $i <= 4; $i++) {
            $room->beds()->create(['bed_label' => "Bed {$i}", 'status' => 'vacant']);
        }
        $room->syncFromRoomType();
        $room->update(['monthly_utility_cost' => 2000, 'monthly_wifi_cost' => 400]);

        return $room->fresh();
    }

    /** A tenant whose paid move-in fee held $advanceMonths of advance and 1 month deposit. */
    private function contract(string $start, int $advanceMonths = 1, float $rate = 4000): LeaseContract
    {
        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana' . random_int(1, 99999) . '@test.ph', 'contact_number' => '09170000000', 'status' => 'active']);
        $contract = LeaseContract::create([
            'tenant_id' => $tenant->id,
            'bed_id' => $this->room()->beds->first()->id,
            'start_date' => $start,
            'end_date' => Carbon::parse($start)->addMonths(6)->toDateString(),
            'monthly_rate' => $rate,
            'esign_status' => 'signed',
            'status' => 'active',
        ]);
        BillingStatement::create([
            'contract_id' => $contract->id, 'tenant_id' => $tenant->id, 'type' => 'move_in',
            'billing_period_start' => $start, 'billing_period_end' => $start, 'due_date' => $start,
            'base_rent' => $rate * ($advanceMonths + 1), 'total_amount' => $rate * ($advanceMonths + 1),
            'advance_amount' => $rate * $advanceMonths, 'deposit_amount' => $rate, 'status' => 'paid',
        ]);

        return $contract;
    }

    private function firstBill(LeaseContract $contract, string $today): BillingStatement
    {
        Carbon::setTestNow($today);
        app(BillingController::class)->generate(new Request());

        return BillingStatement::where('contract_id', $contract->id)->where('type', 'monthly')->orderBy('billing_period_start')->firstOrFail();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        return $admin;
    }

    private function policy(array $overrides = []): array
    {
        return $overrides + [
            'rent_due_day' => 1, 'grace_period_days' => 3, 'late_penalty_percent' => 10, 'minimum_stay_months' => 3,
            'move_out_notice_days' => 14, 'extension_notice_days' => 30, 'deposit_refund_days' => 21,
            'reservation_validity_days' => 30, 'mid_month_move_in' => 'full',
            'water_included' => false, 'electricity_included' => false, 'wifi_included' => false,
            'advance_rent_months' => 1, 'security_deposit_months' => 1,
        ];
    }

    public function test_admin_sets_advance_and_deposit_months_but_not_both_zero(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/dormitory-profile/policy', $this->policy(['advance_rent_months' => 2, 'security_deposit_months' => 1]))->assertOk();
        $this->assertSame(2, DormitoryProfile::current()->advance_rent_months);

        $this->actingAs($admin)->postJson('/dormitory-profile/policy', $this->policy(['advance_rent_months' => 0, 'security_deposit_months' => 0]))->assertStatus(422);
        $this->actingAs($admin)->postJson('/dormitory-profile/policy', $this->policy(['advance_rent_months' => 4]))->assertStatus(422);
    }

    public function test_one_month_advance_still_bills_from_the_next_month(): void
    {
        $bill = $this->firstBill($this->contract('2026-10-01', 1), '2026-10-25 08:00:00');

        $this->assertSame('2026-11-01', $bill->billing_period_start->toDateString());
    }

    public function test_two_months_advance_pays_the_first_two_months(): void
    {
        $contract = $this->contract('2026-10-01', 2);

        // On Oct 25 November's bill would normally be issued; it's covered.
        Carbon::setTestNow('2026-10-25 08:00:00');
        app(BillingController::class)->generate(new Request());
        $this->assertFalse(BillingStatement::where('contract_id', $contract->id)->where('type', 'monthly')->exists());

        $bill = $this->firstBill($contract, '2026-11-25 08:00:00');
        $this->assertSame('2026-12-01', $bill->billing_period_start->toDateString());
        $this->assertSame(4000.0, (float) $bill->base_rent);
    }

    public function test_no_advance_bills_the_move_in_month_prorated_when_the_dorm_prorates(): void
    {
        DormitoryProfile::current()->update(['mid_month_move_in' => 'prorated']);
        $bill = $this->firstBill($this->contract('2026-10-17', 0, 3100), '2026-10-17 08:00:00');

        $this->assertSame('2026-10-17', $bill->billing_period_start->toDateString());
        $this->assertSame(1500.0, (float) $bill->base_rent); // 15 of 31 days
    }

    public function test_included_utilities_change_the_monthly_total(): void
    {
        $profile = DormitoryProfile::current();

        $profile->update(['water_included' => false, 'electricity_included' => false, 'wifi_included' => false]);
        $none = $this->firstBill($this->contract('2026-10-01'), '2026-10-25 08:00:00');
        $this->assertSame([500.0, 100.0, 4600.0], [(float) $none->utilities_amount, (float) $none->wifi_amount, (float) $none->total_amount]);

        $profile->update(['water_included' => true, 'electricity_included' => true, 'wifi_included' => false]);
        $utilities = $this->firstBill($this->contract('2026-10-01'), '2026-10-25 08:00:00');
        $this->assertSame([0.0, 100.0, 4100.0], [(float) $utilities->utilities_amount, (float) $utilities->wifi_amount, (float) $utilities->total_amount]);

        $profile->update(['water_included' => true, 'electricity_included' => true, 'wifi_included' => true]);
        $all = $this->firstBill($this->contract('2026-10-01'), '2026-10-25 08:00:00');
        $this->assertSame(4000.0, (float) $all->total_amount);
    }

    public function test_tenant_sees_advance_coverage_and_held_deposit(): void
    {
        $contract = $this->contract('2026-10-01', 2);
        $summary = DepositRefund::summaryFor($contract->tenant);

        $this->assertSame(8000.0, $summary['advance_amount']);
        $this->assertSame(2, $summary['advance_months']);
        $this->assertSame('Oct 1 – Nov 30, 2026', $summary['advance_covers']);
        $this->assertSame('held', $summary['deposit_status']);
    }

    public function test_deposit_fully_used_for_unpaid_rent_shows_nothing_to_refund_and_what_is_still_owed(): void
    {
        $contract = $this->contract('2026-10-01');
        BillingStatement::create([
            'contract_id' => $contract->id, 'tenant_id' => $contract->tenant_id, 'type' => 'monthly',
            'billing_period_start' => '2026-11-01', 'billing_period_end' => '2026-11-30', 'due_date' => '2026-11-01',
            'base_rent' => 1500, 'utilities_amount' => 0, 'wifi_amount' => 0, 'penalty_amount' => 0,
            'total_amount' => 1500, 'status' => 'overdue',
        ]);
        DepositRefund::create([
            'tenant_id' => $contract->tenant_id, 'deposit_amount' => 4000, 'deductions_amount' => 4000,
            'deductions_note' => 'Unpaid October rent', 'refund_amount' => 0, 'refund_method' => 'None', 'refunded_at' => '2026-11-05',
        ]);

        $summary = DepositRefund::summaryFor($contract->tenant);
        $this->assertSame('fully_deducted', $summary['deposit_status']);
        $this->assertSame(0.0, $summary['refund']['refund_amount']);
        $this->assertSame(1500.0, $summary['still_owed']);
    }

    public function test_dashboard_shows_the_deposit_and_advance_card(): void
    {
        $contract = $this->contract('2026-10-01', 2);
        $user = User::factory()->create();
        $user->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'tenant'])->id])->save();
        $contract->tenant->update(['user_id' => $user->id]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Deposit &amp; Advance', false)
            ->assertSee('Oct 1 – Nov 30, 2026')
            ->assertSee('Held');
    }
}
