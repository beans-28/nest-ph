<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BillingController;
use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\EscalationLog;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\EscalationService;
use App\Services\TextbeeService;
use Carbon\Carbon;
use Database\Seeders\PurezaStationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The system follows the dorm's signed documents: room types and rates,
 * rent due on the 1st, a 3-day grace period, a one-time 10% late penalty,
 * the 3-month minimum stay, emergency-contact consent, and the documents
 * applicants sign.
 */
class DormitoryDocumentsAlignmentTest extends TestCase
{
    use RefreshDatabase;

    private const SIGNATURE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

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

    private function roomOfType(string $name, string $location, int $beds): Room
    {
        $type = RoomType::where('name', $name)->where('location', $location)->firstOrFail();
        $floor = Floor::firstOrCreate(['floor_number' => 2], ['floor_name' => 'Floor 2']);
        $room = $floor->rooms()->create(['room_no' => 'R' . random_int(100, 999), 'room_type_id' => $type->id, 'status' => 'available']);
        for ($i = 1; $i <= $beds; $i++) {
            $room->beds()->create(['bed_label' => "Bed {$i}", 'status' => 'vacant']);
        }
        $room->syncFromRoomType();

        return $room->fresh();
    }

    private function tenantWithContract(string $start, float $rate = 4200, bool $withMoveInBill = true): LeaseContract
    {
        $room = $this->roomOfType('Room with AC, 4 persons', '2nd to 5th floor', 4);
        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana' . random_int(1, 99999) . '@test.ph', 'contact_number' => '09170000000', 'status' => 'active']);
        $contract = LeaseContract::create([
            'tenant_id' => $tenant->id,
            'bed_id' => $room->beds->first()->id,
            'start_date' => $start,
            'end_date' => DormitoryProfile::current()->minimumEndDate(Carbon::parse($start))->toDateString(),
            'monthly_rate' => $rate,
            'esign_status' => 'signed',
            'status' => 'active',
        ]);

        if ($withMoveInBill) {
            BillingStatement::create([
                'contract_id' => $contract->id, 'tenant_id' => $tenant->id, 'type' => 'move_in',
                'billing_period_start' => $start, 'billing_period_end' => $start, 'due_date' => $start,
                'base_rent' => $rate * 2, 'total_amount' => $rate * 2, 'status' => 'paid',
            ]);
        }

        return $contract;
    }

    private function monthlyBill(LeaseContract $contract, string $periodStart, float $rent = 4200): BillingStatement
    {
        $start = Carbon::parse($periodStart);

        return BillingStatement::create([
            'contract_id' => $contract->id, 'tenant_id' => $contract->tenant_id, 'type' => 'monthly',
            'billing_period_start' => $start, 'billing_period_end' => $start->copy()->endOfMonth(),
            'due_date' => $start, 'base_rent' => $rent, 'utilities_amount' => 0, 'wifi_amount' => 0,
            'penalty_amount' => 0, 'total_amount' => $rent, 'status' => 'unpaid',
        ]);
    }

    public function test_room_types_set_the_price_each_tenant_pays(): void
    {
        $perBed = $this->roomOfType('Room with AC, 10–16 persons', '2nd to 5th floor', 12);
        $this->assertSame(3800.0, $perBed->perBedRate());
        $this->assertSame(45600.0, (float) $perBed->monthly_rate); // whole-room total, for reports and sorting
        $this->assertSame('Room with AC, 10–16 persons', $perBed->room_type);

        $solo = $this->roomOfType('Solo fan room', 'Ground floor', 1);
        $this->assertSame(4500.0, $solo->perBedRate());
    }

    public function test_minimum_stay_ends_on_the_last_day_of_the_third_month(): void
    {
        $profile = DormitoryProfile::current();

        $this->assertSame('2026-12-31', $profile->minimumEndDate(Carbon::parse('2026-10-01'))->toDateString());
        $this->assertSame('2027-01-31', $profile->minimumEndDate(Carbon::parse('2026-10-20'))->toDateString());
    }

    public function test_first_monthly_bill_is_for_the_month_after_move_in_and_due_on_the_1st(): void
    {
        $contract = $this->tenantWithContract('2026-10-20');

        Carbon::setTestNow('2026-11-01 08:00:00');
        app(BillingController::class)->generate(new Request());

        $bill = BillingStatement::where('contract_id', $contract->id)->where('type', 'monthly')->sole();
        $this->assertSame('2026-11-01', $bill->billing_period_start->toDateString());
        $this->assertSame('2026-11-30', $bill->billing_period_end->toDateString());
        $this->assertSame('2026-11-01', $bill->due_date->toDateString());
        $this->assertSame(4200.0, (float) $bill->base_rent); // advance paid October in full
    }

    public function test_prorated_move_in_credits_the_unused_days_on_the_first_bill(): void
    {
        DormitoryProfile::current()->update(['mid_month_move_in' => 'prorated']);
        $contract = $this->tenantWithContract('2026-10-20', 3100);

        Carbon::setTestNow('2026-11-01 08:00:00');
        app(BillingController::class)->generate(new Request());

        $bill = BillingStatement::where('contract_id', $contract->id)->where('type', 'monthly')->sole();
        // 19 unused October days out of 31: 3100 - 3100 * 19/31 = 1200
        $this->assertSame(1200.0, (float) $bill->base_rent);
    }

    public function test_bill_is_not_overdue_during_the_grace_period(): void
    {
        $bill = $this->monthlyBill($this->tenantWithContract('2026-09-01'), '2026-10-01');

        Carbon::setTestNow('2026-10-04 23:00:00');
        BillingStatement::syncOverdueStatuses();

        $this->assertSame('unpaid', $bill->fresh()->status);
        $this->assertSame(0, Penalty::count());
    }

    public function test_one_time_ten_percent_penalty_after_the_grace_period(): void
    {
        $bill = $this->monthlyBill($this->tenantWithContract('2026-09-01'), '2026-10-01');

        Carbon::setTestNow('2026-10-05 08:00:00');
        BillingStatement::syncOverdueStatuses();
        BillingStatement::syncOverdueStatuses(); // never charged twice

        $bill->refresh();
        $this->assertSame('overdue', $bill->status);
        $penalty = Penalty::where('billing_id', $bill->id)->sole();
        $this->assertSame('late_payment', $penalty->type);
        $this->assertSame(420.0, (float) $penalty->amount);
        $this->assertSame(4620.0, (float) $bill->total_amount);
    }

    public function test_penalty_is_only_on_the_unpaid_part_of_the_rent(): void
    {
        $bill = $this->monthlyBill($this->tenantWithContract('2026-09-01'), '2026-10-01');
        Payment::create(['billing_id' => $bill->id, 'tenant_id' => $bill->tenant_id, 'amount_paid' => 2200, 'payment_method' => 'cash',
            'payment_date' => '2026-10-02', 'status' => 'approved', 'created_at' => now()]);
        $bill->update(['status' => 'partial']);

        Carbon::setTestNow('2026-10-06 08:00:00');
        BillingStatement::syncOverdueStatuses();

        $this->assertSame(200.0, (float) Penalty::where('billing_id', $bill->id)->value('amount')); // 10% of the 2,000 left
    }

    public function test_proof_dated_within_grace_waits_for_review_then_waives_nothing_needed(): void
    {
        $bill = $this->monthlyBill($this->tenantWithContract('2026-09-01'), '2026-10-01');
        $payment = Payment::create(['billing_id' => $bill->id, 'tenant_id' => $bill->tenant_id, 'amount_paid' => 4200, 'payment_method' => 'gcash',
            'payment_date' => '2026-10-03', 'status' => 'pending', 'created_at' => now()]);

        Carbon::setTestNow('2026-10-07 08:00:00');
        BillingStatement::syncOverdueStatuses();
        $this->assertSame(0, Penalty::count()); // proof dated on time is still under review

        $payment->update(['status' => 'approved']);
        BillingStatement::syncOverdueStatuses();
        $this->assertSame(0, Penalty::count());
    }

    public function test_late_review_of_an_on_time_proof_waives_the_penalty(): void
    {
        $bill = $this->monthlyBill($this->tenantWithContract('2026-09-01'), '2026-10-01');

        Carbon::setTestNow('2026-10-08 08:00:00');
        BillingStatement::syncOverdueStatuses();
        $this->assertSame(1, Penalty::where('status', 'active')->count());

        // Tenant actually paid on Oct 3 (sent proof by email, recorded later).
        Payment::create(['billing_id' => $bill->id, 'tenant_id' => $bill->tenant_id, 'amount_paid' => 4200, 'payment_method' => 'gcash',
            'payment_date' => '2026-10-03', 'status' => 'approved', 'created_at' => now()]);

        $this->assertTrue($bill->fresh()->waiveLatePenaltyIfPaidOnTime());
        $this->assertSame('waived', Penalty::sole()->status);
        $this->assertSame(4200.0, (float) $bill->fresh()->total_amount);
    }

    public function test_emergency_contact_gets_no_billing_sms_without_consent(): void
    {
        $sms = $this->mock(TextbeeService::class);
        $sms->shouldReceive('send')->andReturn(true);

        $contract = $this->tenantWithContract('2026-09-01');
        $contract->tenant->update(['emergency_contact_number' => '09181111111', 'emergency_billing_reminders' => false]);
        $bill = $this->monthlyBill($contract, '2026-10-01');

        Carbon::setTestNow('2026-10-25 08:00:00');
        app(EscalationService::class)->processBillingStatement($bill);

        $log = EscalationLog::where('billing_id', $bill->id)->where('action_type', 'emergency_contact_notified')->sole();
        $this->assertSame('resolved', $log->status);
        $this->assertStringStartsWith('Skipped', $log->message_content);
        $sms->shouldNotHaveReceived('send', ['09181111111', \Mockery::any()]);
    }

    public function test_consenting_emergency_contact_message_has_no_tenant_details(): void
    {
        $sms = $this->mock(TextbeeService::class);
        $sms->shouldReceive('send')->andReturn(true);

        $contract = $this->tenantWithContract('2026-09-01');
        $contract->tenant->update(['emergency_contact_number' => '09181111111', 'emergency_billing_reminders' => true]);
        $bill = $this->monthlyBill($contract, '2026-10-01');

        Carbon::setTestNow('2026-10-25 08:00:00');
        app(EscalationService::class)->processBillingStatement($bill);

        $message = EscalationLog::where('billing_id', $bill->id)->where('action_type', 'emergency_contact_notified')->value('message_content');
        $this->assertStringContainsString('amount due PHP', $message);
        $this->assertStringNotContainsString('Ana', $message);
        $this->assertStringNotContainsString('Cruz', $message);
    }

    private function applicant(Bed $bed): array
    {
        return [
            'first_name' => 'Bea', 'last_name' => 'Santos', 'birthdate' => '2004-05-01',
            'home_address' => 'Quezon City', 'contact_number' => '09170001111', 'email' => 'bea@test.ph',
            'emergency_contact_name' => 'Lito Santos', 'emergency_contact_number' => '09170002222', 'emergency_contact_relation' => 'parent',
            'bed_id' => $bed->id, 'preferred_start_date' => now()->addDays(10)->toDateString(), 'tenant_end_date' => '',
            'type_of_tenant' => 'student',
        ];
    }

    public function test_applicant_reads_signs_and_submits_with_both_signatures(): void
    {
        Storage::fake('public');
        $bed = $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6)->beds->first();
        $applicant = $this->applicant($bed);

        // The Apply page shows each document as a real PDF.
        foreach (['agreement', 'rules', 'fees'] as $doc) {
            $pdf = $this->postJson('/api/applications/contract-preview', $applicant + ['document' => $doc])->assertOk();
            $this->assertStringStartsWith('%PDF', $pdf->getContent());
        }

        // Word for word from the dorm's files, with the blanks filled in.
        $documents = app(\App\Services\TenancyDocuments::class);
        $data = $documents->data($applicant, $bed);
        $agreement = $documents->html('agreement', $data);
        $this->assertStringContainsString('Bea Santos', $agreement);
        // "Entered into on the __ day of __": today's date while previewing.
        $this->assertStringContainsString('<span class="fill">' . now()->format('jS') . '</span> day of <span class="fill">' . now()->format('F') . '</span>', $agreement);
        // A blank copy (no applicant) keeps the blanks.
        $this->assertStringContainsString('____ day of ______________', $documents->html('agreement', $documents->data()));
        $this->assertStringContainsString('4,000.00', $agreement);
        $this->assertStringContainsString('☑</span> 6-person AC', $agreement);
        $this->assertStringContainsString('<strong>Curfew</strong> is from 11:00 PM to 4:00 AM', $documents->html('rules', $data));
        $this->assertStringContainsString('09178932970', $documents->html('fees', $data));

        $this->postJson('/api/applications/contract-sign', $applicant + [
            'signature_image' => self::SIGNATURE,
            'acknowledged' => ['agreement', 'rules', 'fees'],
            'emergency_billing_consent' => true,
        ])->assertStatus(422); // emergency contact hasn't signed

        $signed = $this->postJson('/api/applications/contract-sign', $applicant + [
            'signature_image' => self::SIGNATURE,
            'emergency_signature_image' => self::SIGNATURE,
            'acknowledged' => ['agreement', 'rules', 'fees'],
            'emergency_billing_consent' => true,
        ])->assertOk();

        $path = $signed->json('signed_contract_path');
        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('%PDF', Storage::disk('public')->get($path));

        $this->post('/api/applications', $applicant + [
            'signed_contract_path' => $path, 'contract_acceptance' => '1', 'dpa_consent' => '1',
            'emergency_contact_id' => \Illuminate\Http\UploadedFile::fake()->image('guardian-id.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $application = \App\Models\Application::sole();
        $this->assertTrue($application->emergency_contact_signed);
        $this->assertTrue($application->emergency_billing_consent);
        Storage::disk('public')->assertExists($application->emergency_contact_id_path);
    }

    public function test_emergency_contact_valid_id_is_required(): void
    {
        $bed = $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6)->beds->first();

        $this->post('/api/applications', $this->applicant($bed) + [
            'contract_acceptance' => '1', 'dpa_consent' => '1',
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('emergency_contact_id');
    }

    public function test_changing_details_after_signing_requires_signing_again(): void
    {
        Storage::fake('public');
        $bed = $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6)->beds->first();
        $applicant = $this->applicant($bed);

        $path = $this->postJson('/api/applications/contract-sign', $applicant + [
            'signature_image' => self::SIGNATURE, 'emergency_signature_image' => self::SIGNATURE,
            'acknowledged' => ['agreement', 'rules', 'fees'], 'emergency_billing_consent' => false,
        ])->json('signed_contract_path');

        $this->post('/api/applications', array_merge($applicant, ['last_name' => 'Reyes']) + [
            'signed_contract_path' => $path, 'contract_acceptance' => '1', 'dpa_consent' => '1',
        ], ['Accept' => 'application/json'])->assertStatus(422);
    }

    public function test_applicants_must_be_18_and_respect_the_minimum_stay(): void
    {
        $bed = $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6)->beds->first();
        $applicant = $this->applicant($bed);

        $this->post('/api/applications', array_merge($applicant, ['birthdate' => now()->subYears(17)->toDateString()]) + [
            'contract_acceptance' => '1', 'dpa_consent' => '1',
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('birthdate');

        $this->post('/api/applications', array_merge($applicant, ['tenant_end_date' => now()->addMonth()->toDateString()]) + [
            'contract_acceptance' => '1', 'dpa_consent' => '1',
            'emergency_contact_id' => \Illuminate\Http\UploadedFile::fake()->image('guardian-id.jpg'),
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('tenant_end_date');
    }

    public function test_only_the_rules_are_public(): void
    {
        // Dorm Info shows (and downloads) the Rules and Regulations.
        $this->get('/dorm-info/policies-file')->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get('/dorm-info/policies-file/download')->assertOk();

        // The Agreement and the Fees Schedule (owner's bank accounts) are not.
        $this->get('/documents/agreement')->assertNotFound();
        $this->get('/documents/fees')->assertNotFound();
        $this->get('/dormitory-profile/documents/fees')->assertRedirect();

        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        foreach (['agreement', 'rules', 'fees'] as $doc) {
            $this->actingAs($admin)->get("/dormitory-profile/documents/{$doc}")->assertOk()->assertHeader('content-type', 'application/pdf');
        }
    }

    public function test_admin_can_manage_room_types_and_policy(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        $room = $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6);
        $type = $room->roomType;

        $this->actingAs($admin)->patchJson("/dormitory-profile/room-types/{$type->id}", [
            'name' => $type->name, 'location' => $type->location, 'pricing_mode' => 'per_bed', 'monthly_rate' => 4100,
        ])->assertOk();
        $this->assertSame(4100.0, $room->fresh()->perBedRate());

        $this->actingAs($admin)->deleteJson("/dormitory-profile/room-types/{$type->id}")->assertStatus(409); // still in use

        $this->actingAs($admin)->postJson('/dormitory-profile/policy', [
            'rent_due_day' => 1, 'grace_period_days' => 5, 'late_penalty_percent' => 5, 'minimum_stay_months' => 3,
            'move_out_notice_days' => 14, 'extension_notice_days' => 30, 'deposit_refund_days' => 21,
            'reservation_validity_days' => 30, 'mid_month_move_in' => 'full',
            'water_included' => true, 'electricity_included' => true, 'wifi_included' => false,
        ])->assertOk();
        $this->assertSame(5, DormitoryProfile::current()->grace_period_days);
    }

    public function test_edited_pages_still_render(): void
    {
        $this->get('/apply')->assertOk()->assertSee('Read &amp; Sign Documents', false)->assertSee('emergencySignatureCanvas');
        $this->get('/dorm-info')->assertOk()->assertSee('Rules and Regulations')->assertDontSee('Documents you will sign');

        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        foreach (['manage_tenants', 'manage_rooms', 'manage_contracts', 'manage_billing', 'manage_users', 'view_reports'] as $privilege) {
            \App\Models\AdminPrivilege::create(['user_id' => $admin->id, 'privilege_name' => $privilege]);
        }
        $this->roomOfType('Room with AC, 6 persons', '2nd to 5th floor', 6);

        $this->actingAs($admin)->get('/dormitory-profile')->assertOk()->assertSee('Room Types &amp; Rates', false)->assertSee('Rental Policy');
        $this->actingAs($admin)->get('/vacancy-monitoring')->assertOk()->assertSee('newRoomTypeSelect');
        $this->actingAs($admin)->get('/payments')->assertOk()->assertSee('apChargeSelect');
    }
}
