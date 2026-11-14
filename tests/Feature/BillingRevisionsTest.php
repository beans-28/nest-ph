<?php

namespace Tests\Feature;

use App\Mail\BillingNoticeMail;
use App\Models\BillingEmailLog;
use App\Models\BillingStatement;
use App\Models\DepositRefund;
use App\Models\DormitoryProfile;
use App\Models\EscalationLog;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Penalty;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Services\EscalationService;
use App\Services\TextbeeService;
use Carbon\Carbon;
use Database\Seeders\PurezaStationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Pureza client revisions (2026-11-14), section 3 "Billing, Payments, and
 * Delinquency Management":
 *  - billing due-date and overdue notices also go out by email
 *  - guardians (emergency contacts who consented) get the same overdue notices
 *  - penalties are paid from the security deposit first
 */
class BillingRevisionsTest extends TestCase
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

    /** A tenant with a lease and (optionally) a paid move-in fee holding $deposit. */
    private function tenant(float $deposit = 4200): Tenant
    {
        $type = RoomType::where('name', 'Room with AC, 4 persons')->where('location', '2nd to 5th floor')->firstOrFail();
        $floor = Floor::firstOrCreate(['floor_number' => 2], ['floor_name' => 'Floor 2']);
        $room = Room::create(['floor_id' => $floor->id, 'room_no' => 'R' . random_int(100, 99999), 'room_type_id' => $type->id, 'capacity' => 4, 'status' => 'available']);
        $room->syncFromRoomType();
        $bed = $room->beds()->first() ?? $room->beds()->create(['bed_label' => 'Bed 1', 'status' => 'occupied']);

        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana' . random_int(1, 99999) . '@test.ph',
            'contact_number' => '09170000000', 'status' => 'active']);
        $contract = LeaseContract::create(['tenant_id' => $tenant->id, 'bed_id' => $bed->id, 'start_date' => '2026-09-01',
            'end_date' => '2027-08-31', 'monthly_rate' => 4200, 'esign_status' => 'signed', 'status' => 'active']);

        if ($deposit > 0) {
            BillingStatement::create(['contract_id' => $contract->id, 'tenant_id' => $tenant->id, 'type' => 'move_in',
                'billing_period_start' => '2026-09-01', 'billing_period_end' => '2026-09-01', 'due_date' => '2026-09-01',
                'base_rent' => 4200 + $deposit, 'advance_amount' => 4200, 'deposit_amount' => $deposit,
                'total_amount' => 4200 + $deposit, 'status' => 'paid']);
        }

        return $tenant;
    }

    private function bill(Tenant $tenant, string $due, float $rent = 4200): BillingStatement
    {
        $start = Carbon::parse($due);

        return BillingStatement::create(['contract_id' => $tenant->contracts()->first()->id, 'tenant_id' => $tenant->id, 'type' => 'monthly',
            'billing_period_start' => $start, 'billing_period_end' => $start->copy()->endOfMonth(), 'due_date' => $start,
            'base_rent' => $rent, 'utilities_amount' => 0, 'wifi_amount' => 0, 'penalty_amount' => 0, 'total_amount' => $rent, 'status' => 'unpaid']);
    }

    // ----- Penalties from the security deposit -----

    public function test_late_penalty_is_taken_from_the_deposit_instead_of_the_bill(): void
    {
        $tenant = $this->tenant(4200);
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-10-05 08:00:00');
        BillingStatement::syncOverdueStatuses();

        $penalty = Penalty::where('billing_id', $bill->id)->sole();
        $this->assertSame(420.0, (float) $penalty->amount);
        $this->assertSame(420.0, (float) $penalty->deposit_deducted);
        $this->assertSame(4200.0, (float) $bill->fresh()->total_amount); // nothing added to the bill
        $this->assertSame(3780.0, DepositRefund::depositRemainingFor($tenant));
    }

    public function test_when_the_deposit_runs_out_only_the_rest_is_billed(): void
    {
        $tenant = $this->tenant(300);
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-10-05 08:00:00');
        BillingStatement::syncOverdueStatuses();

        $penalty = Penalty::where('billing_id', $bill->id)->sole();
        $this->assertSame(300.0, (float) $penalty->deposit_deducted);
        $this->assertSame(4320.0, (float) $bill->fresh()->total_amount); // 4200 + the 120 the deposit couldn't cover
        $this->assertSame(0.0, DepositRefund::depositRemainingFor($tenant));

        // Deposit used up: the next penalty follows the normal flow.
        $next = Penalty::create(['tenant_id' => $tenant->id, 'type' => 'manual', 'description' => 'Rule violation',
            'amount' => 500, 'status' => 'active']);
        $this->assertSame(0.0, (float) $next->deposit_deducted);
    }

    public function test_waiving_a_penalty_gives_the_money_back_to_the_deposit(): void
    {
        $tenant = $this->tenant(1000);
        $penalty = Penalty::create(['tenant_id' => $tenant->id, 'type' => 'manual', 'description' => 'Broken lock',
            'amount' => 600, 'status' => 'active']);
        $this->assertSame(400.0, DepositRefund::depositRemainingFor($tenant));

        $penalty->update(['status' => 'waived']);
        $this->assertSame(1000.0, DepositRefund::depositRemainingFor($tenant));
    }

    public function test_no_deposit_means_the_normal_flow(): void
    {
        $tenant = $this->tenant(0);
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-10-05 08:00:00');
        BillingStatement::syncOverdueStatuses();

        $this->assertSame(0.0, (float) Penalty::where('billing_id', $bill->id)->sole()->deposit_deducted);
        $this->assertSame(4620.0, (float) $bill->fresh()->total_amount);
    }

    // ----- Email notices -----

    public function test_due_soon_and_grace_reminders_are_emailed_once(): void
    {
        Mail::fake();
        $tenant = $this->tenant();
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-09-28 08:00:00'); // 3 days before
        $this->artisan('billing:send-reminders')->assertSuccessful();
        $this->artisan('billing:send-reminders')->assertSuccessful();
        Mail::assertSent(BillingNoticeMail::class, 1);

        Carbon::setTestNow('2026-10-03 08:00:00'); // in the grace period
        $this->artisan('billing:send-reminders')->assertSuccessful();
        Mail::assertSent(BillingNoticeMail::class, 2);
        $this->assertSame(['due_soon', 'grace'], BillingEmailLog::where('billing_id', $bill->id)->orderBy('id')->pluck('kind')->all());
    }

    public function test_overdue_notices_are_emailed_and_sent_to_a_consenting_guardian(): void
    {
        Mail::fake();
        $sms = $this->mock(TextbeeService::class);
        $sms->shouldReceive('send')->andReturn(true);

        $tenant = $this->tenant();
        $tenant->update(['emergency_contact_number' => '09181111111', 'emergency_contact_email' => 'mom@test.ph', 'emergency_billing_reminders' => true]);
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-10-06 08:00:00'); // day 2 after the grace period
        app(EscalationService::class)->processBillingStatement($bill);

        Mail::assertSent(BillingNoticeMail::class, fn ($m) => $m->hasTo($tenant->email) && ! $m->forGuardian);
        Mail::assertSent(BillingNoticeMail::class, fn ($m) => $m->hasTo('mom@test.ph') && $m->forGuardian);
        $sms->shouldHaveReceived('send')->with('09181111111', \Mockery::on(fn ($text) => ! str_contains($text, 'Ana')));
        $this->assertSame('sent', EscalationLog::where('billing_id', $bill->id)->where('action_type', 'guardian_overdue_day2')->sole()->status);
    }

    public function test_guardian_without_consent_is_not_contacted(): void
    {
        Mail::fake();
        $sms = $this->mock(TextbeeService::class);
        $sms->shouldReceive('send')->andReturn(true);

        $tenant = $this->tenant();
        $tenant->update(['emergency_contact_number' => '09181111111', 'emergency_contact_email' => 'mom@test.ph', 'emergency_billing_reminders' => false]);
        $bill = $this->bill($tenant, '2026-10-01');

        Carbon::setTestNow('2026-10-06 08:00:00');
        app(EscalationService::class)->processBillingStatement($bill);

        Mail::assertNotSent(BillingNoticeMail::class, fn ($m) => $m->hasTo('mom@test.ph'));
        $sms->shouldNotHaveReceived('send', ['09181111111', \Mockery::any()]);
    }
}
