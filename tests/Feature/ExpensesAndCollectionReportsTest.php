<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\BillingStatement;
use App\Models\Expense;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Payment;
use App\Models\RecurringExpense;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PurezaStationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pureza client revisions (2026-11-14), section 4 "Financial Management
 * and Reports":
 *  - expenses are recorded one by one, with a receipt / proof of purchase
 *  - recurring expenses (e.g. salaries every payday) record themselves
 *  - expenses show up in the financial reports automatically
 *  - downloadable delinquent tenant PDF
 *  - daily collection report with cash on hand / cash for bank deposit
 */
class ExpensesAndCollectionReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-14 10:00:00');
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function admin(array $privileges = ['view_reports']): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        foreach ($privileges as $p) {
            AdminPrivilege::create(['user_id' => $admin->id, 'privilege_name' => $p]);
        }

        return $admin;
    }

    private function newExpense(array $overrides = []): array
    {
        return $overrides + [
            'category' => 'equipment',
            'description' => 'Electric fan for Room 204',
            'amount' => 1850,
            'date_paid' => '2026-11-10',
            'payment_method' => 'cash',
        ];
    }

    // ----- Recording expenses with receipts -----

    public function test_admin_records_a_purchase_with_a_receipt(): void
    {
        $res = $this->actingAs($this->admin())->post('/reports/expenses', $this->newExpense([
            'receipt' => UploadedFile::fake()->image('receipt.jpg'),
        ]), ['Accept' => 'application/json'])->assertCreated();

        $expense = Expense::firstOrFail();
        $this->assertSame('equipment', $expense->category);
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);
        $this->assertNotNull($res->json('expense.receipt_url'));
    }

    public function test_receipt_must_be_an_image_or_pdf_up_to_5mb_and_date_cannot_be_in_the_future(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/reports/expenses', $this->newExpense(['receipt' => UploadedFile::fake()->create('r.exe', 10)]),
            ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('receipt');
        $this->actingAs($admin)->post('/reports/expenses', $this->newExpense(['receipt' => UploadedFile::fake()->create('r.pdf', 6000, 'application/pdf')]),
            ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('receipt');
        $this->actingAs($admin)->post('/reports/expenses', $this->newExpense(['date_paid' => '2026-11-20']),
            ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('date_paid');
        $this->assertSame(0, Expense::count());
    }

    public function test_replacing_and_deleting_a_receipt_removes_the_old_file(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/reports/expenses', $this->newExpense(['receipt' => UploadedFile::fake()->image('a.jpg')]),
            ['Accept' => 'application/json'])->assertCreated();
        $expense = Expense::firstOrFail();
        $old = $expense->receipt_path;

        $this->actingAs($admin)->post("/reports/expenses/{$expense->id}", $this->newExpense([
            'amount' => 1900, 'receipt' => UploadedFile::fake()->create('b.pdf', 100, 'application/pdf'),
        ]), ['Accept' => 'application/json'])->assertOk();
        $expense->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($expense->receipt_path);
        $this->assertEquals(1900, $expense->amount);

        $this->actingAs($admin)->deleteJson("/reports/expenses/{$expense->id}")->assertOk();
        Storage::disk('public')->assertMissing($expense->receipt_path);
        $this->assertSame(0, Expense::count());
    }

    // ----- Recurring expenses -----

    public function test_payday_salary_repeats_on_the_15th_and_end_of_month(): void
    {
        // Back-dated: first paid Oct 15. Oct 31 is already due; Nov 15 isn't yet.
        $this->actingAs($this->admin())->post('/reports/expenses', $this->newExpense([
            'category' => 'salaries', 'description' => 'Night guard salary', 'amount' => 7500,
            'date_paid' => '2026-10-15', 'repeat' => '1', 'frequency' => 'payday',
        ]), ['Accept' => 'application/json'])->assertCreated();

        $this->assertEquals(['2026-10-15', '2026-10-31'],
            Expense::orderBy('date_paid')->pluck('date_paid')->map->toDateString()->all());
        $this->assertSame('2026-11-15', RecurringExpense::firstOrFail()->next_date->toDateString());

        // The next day the job runs again: nothing new until the 15th.
        $this->assertSame(0, RecurringExpense::postDue());
        $this->assertSame(1, RecurringExpense::postDue(Carbon::parse('2026-11-15')));
        $this->assertSame(3, Expense::count());
    }

    public function test_monthly_schedule_on_the_31st_falls_on_the_last_day_of_shorter_months(): void
    {
        $this->assertSame('2027-02-28', RecurringExpense::nextAfter(Carbon::parse('2027-01-31'), 'monthly', 31)->toDateString());
        $this->assertSame('2027-03-31', RecurringExpense::nextAfter(Carbon::parse('2027-02-28'), 'monthly', 31)->toDateString());
        $this->assertSame('2026-12-15', RecurringExpense::nextAfter(Carbon::parse('2026-11-30'), 'payday', 0)->toDateString());
    }

    public function test_paused_schedule_records_nothing_and_resumes_from_today(): void
    {
        $schedule = RecurringExpense::create(['category' => 'salaries', 'description' => 'Cleaner', 'amount' => 6500,
            'payment_method' => 'cash', 'frequency' => 'payday', 'next_date' => '2026-10-15', 'active' => false]);
        $this->assertSame(0, RecurringExpense::postDue());

        $this->actingAs($this->admin())->patchJson("/reports/recurring-expenses/{$schedule->id}", ['active' => true])->assertOk();
        // The paused paydays are not back-filled.
        $this->assertSame('2026-11-15', $schedule->fresh()->next_date->toDateString());
        $this->assertSame(0, Expense::count());
    }

    // ----- Expenses flow into the reports -----

    public function test_expenses_are_reflected_in_the_financial_and_expenses_reports(): void
    {
        Expense::create($this->newExpense(['category' => 'repairs', 'amount' => 3000]));
        Expense::create($this->newExpense(['category' => 'salaries', 'amount' => 7500, 'date_paid' => '2026-11-01']));
        // Outside the range: not counted.
        Expense::create($this->newExpense(['amount' => 999, 'date_paid' => '2026-10-31']));

        $admin = $this->admin();
        $res = $this->actingAs($admin)->getJson('/reports/financial?start=2026-11-01&end=2026-11-14')->assertOk();
        $this->assertEquals(10500, $res->json('total_expenses'));
        $this->assertEquals(3000, $res->json('expense_breakdown.repairs'));
        $this->assertEquals(7500, $res->json('expense_breakdown.salaries'));

        $month = $this->actingAs($admin)->getJson('/reports/expenses?month=2026-11')->assertOk();
        $this->assertEquals(10500, $month->json('total'));
        $this->assertCount(2, $month->json('expenses'));

        foreach (['xlsx', 'pdf'] as $format) {
            $this->actingAs($admin)->get("/reports/export?type=expenses&format={$format}")->assertOk();
        }
    }

    // ----- Daily collection -----

    private function payment(Tenant $tenant, BillingStatement $bill, float $amount, string $method, string $date): void
    {
        Payment::create(['billing_id' => $bill->id, 'tenant_id' => $tenant->id, 'amount_paid' => $amount,
            'payment_method' => $method, 'payment_date' => $date, 'status' => 'approved']);
    }

    public function test_daily_collection_shows_payments_and_cash_for_bank_deposit(): void
    {
        $this->seed(PurezaStationSeeder::class);
        $type = RoomType::firstOrFail();
        $floor = Floor::firstOrCreate(['floor_number' => 2], ['floor_name' => 'Floor 2']);
        $room = Room::create(['floor_id' => $floor->id, 'room_no' => '204', 'room_type_id' => $type->id, 'capacity' => 1, 'status' => 'available']);
        $bed = $room->beds()->create(['bed_label' => 'Bed 1', 'status' => 'occupied']);
        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana@test.ph', 'contact_number' => '09170000000', 'status' => 'active']);
        $contract = LeaseContract::create(['tenant_id' => $tenant->id, 'bed_id' => $bed->id, 'start_date' => '2026-09-01',
            'end_date' => '2027-08-31', 'monthly_rate' => 4200, 'esign_status' => 'signed', 'status' => 'active']);
        $bill = BillingStatement::create(['contract_id' => $contract->id, 'tenant_id' => $tenant->id, 'type' => 'monthly',
            'billing_period_start' => '2026-11-01', 'billing_period_end' => '2026-11-30', 'due_date' => '2026-11-05',
            'base_rent' => 4200, 'total_amount' => 4200, 'status' => 'paid']);

        $this->payment($tenant, $bill, 3000, 'cash', '2026-11-14');
        $this->payment($tenant, $bill, 1200, 'gcash', '2026-11-14');
        $this->payment($tenant, $bill, 500, 'cash', '2026-11-13'); // another day
        Expense::create($this->newExpense(['amount' => 800, 'date_paid' => '2026-11-14', 'payment_method' => 'cash']));
        Expense::create($this->newExpense(['amount' => 400, 'date_paid' => '2026-11-14', 'payment_method' => 'gcash']));

        $admin = $this->admin();
        // No dates = today only.
        $res = $this->actingAs($admin)->getJson('/reports/daily')->assertOk();
        $this->assertCount(2, $res->json('payments'));
        $this->assertSame('Ana Cruz', $res->json('payments.0.tenant'));
        $this->assertSame('204', $res->json('payments.0.room'));
        $this->assertEquals(4200, $res->json('total_collected'));
        $this->assertEquals(1200, $res->json('total_expenses'));
        $this->assertEquals(3000, $res->json('remaining'));
        // Only cash goes to the bank: 3,000 cash in - 800 cash out.
        $this->assertEquals(2200, $res->json('cash_for_deposit'));

        $range = $this->actingAs($admin)->getJson('/reports/daily?start=2026-11-13&end=2026-11-14')->assertOk();
        $this->assertCount(2, $range->json('days'));
        $this->assertEquals(500, $range->json('days.0.cash_for_deposit'));

        foreach (['xlsx', 'pdf'] as $format) {
            $this->actingAs($admin)->get("/reports/export?type=daily&format={$format}")->assertOk();
        }
    }

    public function test_reports_page_has_the_new_tabs_and_expense_form(): void
    {
        $this->actingAs($this->admin())->get('/reports')->assertOk()
            ->assertSee('Daily Collection')
            ->assertSee('Receipt / Proof of Purchase')
            ->assertSee('Repeat this expense automatically')
            ->assertSee('Furniture, Appliances &amp; Equipment', false);
    }

    // ----- Delinquent tenant PDF -----

    public function test_delinquency_page_has_the_pdf_button(): void
    {
        $this->actingAs($this->admin(['manage_billing']))->get('/delinquency')->assertOk()
            ->assertSee('Delinquent Tenants PDF');
    }

    public function test_delinquent_tenant_report_downloads_as_pdf(): void
    {
        $this->actingAs($this->admin(['manage_billing']))->get('/delinquency/report.pdf')
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
