<?php

namespace Tests\Feature;

use App\Models\AdminPrivilege;
use App\Models\MonthlyExpense;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Reports > Forecast: the next 3 months estimated from the last 6, shown on
 * the page and exportable to Excel and PDF like the other reports.
 */
class ForecastReportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();
        AdminPrivilege::create(['user_id' => $admin->id, 'privilege_name' => 'view_reports']);

        return $admin;
    }

    public function test_forecast_returns_six_actual_and_three_estimated_months(): void
    {
        MonthlyExpense::create(['month' => now()->startOfMonth()->subMonth()->toDateString(),
            'electricity' => 1000, 'water' => 500, 'internet' => 0, 'salaries' => 0, 'other' => 0]);

        $res = $this->actingAs($this->admin())->getJson('/reports/forecast')->assertOk();

        $this->assertCount(6, $res->json('history'));
        $this->assertCount(3, $res->json('forecast'));
        $this->assertSame(now()->startOfMonth()->addMonth()->format('M Y'), $res->json('forecast.0.label'));
        $this->assertEquals(1500, $res->json('assumptions.avg_expenses'));
        $this->assertEquals(4500, $res->json('totals.expenses'));
    }

    public function test_forecast_exports_to_excel_and_pdf(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/reports/export?type=forecast&format=xlsx')->assertOk();
        $this->actingAs($admin)->get('/reports/export?type=forecast&format=pdf')
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
