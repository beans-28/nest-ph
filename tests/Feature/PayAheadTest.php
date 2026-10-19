<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BillingController;
use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Payment;
use App\Models\Role;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PayAhead;
use App\Services\TenantNotificationService;
use Carbon\Carbon;
use Database\Seeders\PurezaStationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tool defense revision: a tenant can pay future months of RENT ahead.
 * Utilities stay on each month's bill; an unpaid pay-ahead is never owed,
 * overdue, or part of delinquency.
 */
class PayAheadTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $tenantUser;
    private LeaseContract $contract;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PurezaStationSeeder::class);
        Storage::fake('public');
        Carbon::setTestNow('2026-10-20 09:00:00');

        // ₱4,000 rent; ₱500 utilities + ₱100 Wi-Fi per bed. Moved in Oct 1
        // with 1 month advance, so October is paid and November is next.
        $type = RoomType::where('name', 'Room with AC, 4 persons')->where('location', '2nd to 5th floor')->firstOrFail();
        $floor = Floor::firstOrCreate(['floor_number' => 2], ['floor_name' => 'Floor 2']);
        $room = $floor->rooms()->create(['room_no' => 'R201', 'room_type_id' => $type->id, 'status' => 'available']);
        for ($i = 1; $i <= 4; $i++) {
            $room->beds()->create(['bed_label' => "Bed {$i}", 'status' => 'vacant']);
        }
        $room->syncFromRoomType();
        $room->update(['monthly_utility_cost' => 2000, 'monthly_wifi_cost' => 400]);

        $this->tenantUser = User::factory()->create();
        $this->tenantUser->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'tenant'])->id])->save();
        $this->tenant = Tenant::create(['user_id' => $this->tenantUser->id, 'first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana@test.ph', 'contact_number' => '09170000000', 'status' => 'active']);
        $this->contract = LeaseContract::create([
            'tenant_id' => $this->tenant->id, 'bed_id' => $room->beds->first()->id,
            'start_date' => '2026-10-01', 'end_date' => '2027-03-31', 'monthly_rate' => 4000,
            'esign_status' => 'signed', 'status' => 'active',
        ]);
        BillingStatement::create([
            'contract_id' => $this->contract->id, 'tenant_id' => $this->tenant->id, 'type' => 'move_in',
            'billing_period_start' => '2026-10-01', 'billing_period_end' => '2026-10-01', 'due_date' => '2026-10-01',
            'base_rent' => 8000, 'total_amount' => 8000, 'advance_amount' => 4000, 'deposit_amount' => 4000, 'status' => 'paid',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        return $admin;
    }

    private function startPayAhead(int $months): BillingStatement
    {
        $id = $this->actingAs($this->tenantUser)->postJson('/my/billing/pay-ahead', ['months' => $months])
            ->assertCreated()->json('bill.id');

        return BillingStatement::findOrFail($id);
    }

    /** Tenant uploads proof for the full amount; the admin approves it. */
    private function payAndApprove(BillingStatement $bill): void
    {
        $this->actingAs($this->tenantUser)->post("/my/billing/bills/{$bill->id}/payment-proof", [
            'amount_paid' => $bill->total_amount, 'payment_method' => 'gcash', 'payment_date' => '2026-10-20',
            'proof' => UploadedFile::fake()->image('proof.png'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $payment = Payment::where('billing_id', $bill->id)->where('status', 'pending')->sole();
        $this->actingAs($this->admin())->postJson("/payments/{$payment->id}/approve")->assertOk();
    }

    private function generate(string $today): void
    {
        Carbon::setTestNow($today);
        app(BillingController::class)->generate(new Request());
    }

    private function monthly(string $periodStart): BillingStatement
    {
        return BillingStatement::where('contract_id', $this->contract->id)->where('type', 'monthly')
            ->whereDate('billing_period_start', $periodStart)->sole();
    }

    public function test_paid_ahead_months_bill_only_utilities_at_that_months_prices(): void
    {
        $payAhead = $this->startPayAhead(2);
        $this->assertSame('2026-11-01', $payAhead->billing_period_start->toDateString());
        $this->assertSame('2026-12-31', $payAhead->billing_period_end->toDateString());
        $this->assertSame(8000.0, (float) $payAhead->total_amount); // rent only

        $this->payAndApprove($payAhead);
        $this->assertSame('paid', $payAhead->fresh()->status);

        $this->generate('2026-10-25 08:00:00');
        $nov = $this->monthly('2026-11-01');
        $this->assertSame([0.0, 4000.0, 600.0], [(float) $nov->base_rent, (float) $nov->prepaid_rent, (float) $nov->total_amount]);

        // Electricity goes up before December: December's bill uses the new price.
        $this->contract->bed->room->update(['monthly_utility_cost' => 2800]);
        $nov->update(['status' => 'paid']);
        $this->generate('2026-11-25 08:00:00');
        $dec = $this->monthly('2026-12-01');
        $this->assertSame([0.0, 800.0], [(float) $dec->base_rent, (float) $dec->total_amount]);

        // January is back to normal rent.
        $dec->update(['status' => 'paid']);
        $this->generate('2026-12-25 08:00:00');
        $this->assertSame(4000.0, (float) $this->monthly('2027-01-01')->base_rent);
        $this->assertSame(0.0, PayAhead::unusedCredit($this->contract));
    }

    public function test_bill_fully_covered_is_marked_paid(): void
    {
        DormitoryProfile::current()->update(['water_included' => true, 'electricity_included' => true, 'wifi_included' => true]);
        $this->payAndApprove($this->startPayAhead(1));

        $this->generate('2026-10-25 08:00:00');
        $nov = $this->monthly('2026-11-01');
        $this->assertSame(0.0, (float) $nov->total_amount);
        $this->assertSame('paid', $nov->status);
    }

    public function test_bill_issued_before_approval_gets_its_rent_covered_on_approval(): void
    {
        $payAhead = $this->startPayAhead(1);
        $this->generate('2026-10-25 08:00:00'); // November issued while the proof waits
        $this->assertSame(4600.0, (float) $this->monthly('2026-11-01')->total_amount);

        $this->payAndApprove($payAhead);
        $nov = $this->monthly('2026-11-01');
        $this->assertSame([4000.0, 600.0], [(float) $nov->prepaid_rent, (float) $nov->total_amount]);
    }

    public function test_unpaid_pay_ahead_is_never_owed_overdue_or_reminded(): void
    {
        $payAhead = $this->startPayAhead(1);

        Carbon::setTestNow('2026-10-29 09:00:00'); // well past due + grace
        BillingStatement::syncOverdueStatuses();
        $this->assertSame('unpaid', $payAhead->fresh()->status);
        $this->assertFalse(BillingStatement::where('tenant_id', $this->tenant->id)->owed()->exists());
        $this->assertTrue(app(TenantNotificationService::class)->billsNeedingAttention($this->tenant)->isEmpty());
        $this->assertSame(0, (int) $this->actingAs($this->tenantUser)->getJson('/my/billing/bills')->json('summary.total_owed'));
    }

    public function test_cannot_pay_ahead_with_a_bill_due_or_past_the_contract(): void
    {
        $this->actingAs($this->tenantUser)->postJson('/my/billing/pay-ahead', ['months' => 4])->assertStatus(422);

        $this->contract->update(['end_date' => '2026-11-30']);
        $this->actingAs($this->tenantUser)->postJson('/my/billing/pay-ahead', ['months' => 2])
            ->assertStatus(422)->assertJsonFragment(['message' => 'You can pay ahead up to 1 month(s) before your contract ends.']);

        $this->contract->update(['end_date' => '2027-03-31']);
        $this->generate('2026-10-25 08:00:00'); // November bill, unpaid
        $this->actingAs($this->tenantUser)->postJson('/my/billing/pay-ahead', ['months' => 1])->assertStatus(422);
    }

    public function test_next_pay_ahead_starts_after_the_last_one_and_only_one_can_wait(): void
    {
        $first = $this->startPayAhead(1);
        $this->actingAs($this->tenantUser)->postJson('/my/billing/pay-ahead', ['months' => 1])->assertStatus(409);

        $this->payAndApprove($first);
        $second = $this->startPayAhead(2);
        $this->assertSame('2026-12-01', $second->billing_period_start->toDateString());
        $this->assertSame('2027-01-31', $second->billing_period_end->toDateString());
    }

    public function test_must_pay_in_full_and_can_cancel_only_before_paying(): void
    {
        $bill = $this->startPayAhead(2);

        $this->actingAs($this->tenantUser)->post("/my/billing/bills/{$bill->id}/payment-proof", [
            'amount_paid' => 4000, 'payment_method' => 'gcash', 'payment_date' => '2026-10-20',
            'proof' => UploadedFile::fake()->image('proof.png'),
        ], ['Accept' => 'application/json'])->assertStatus(422);

        $this->actingAs($this->tenantUser)->deleteJson("/my/billing/pay-ahead/{$bill->id}")->assertOk();
        $this->assertNull(BillingStatement::find($bill->id));

        $paid = $this->startPayAhead(1);
        $this->payAndApprove($paid);
        $this->actingAs($this->tenantUser)->deleteJson("/my/billing/pay-ahead/{$paid->id}")->assertStatus(409);
    }

    public function test_admin_payments_page_lists_a_pay_ahead_proof(): void
    {
        $bill = $this->startPayAhead(2);
        $this->actingAs($this->tenantUser)->post("/my/billing/bills/{$bill->id}/payment-proof", [
            'amount_paid' => 8000, 'payment_method' => 'gcash', 'payment_date' => '2026-10-20',
            'proof' => UploadedFile::fake()->image('proof.png'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $this->actingAs($this->admin())->get('/payments')->assertOk()->assertSee('Rent ahead: Nov 2026');
    }

    public function test_billing_page_shows_pay_ahead(): void
    {
        $this->actingAs($this->tenantUser)->get('/billing')->assertOk()->assertSee('Pay rent ahead');
    }
}
