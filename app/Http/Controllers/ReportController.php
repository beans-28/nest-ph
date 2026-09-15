<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Use Case Report Table 37 — Generate Reports.
 *
 * Combines both report types (Occupancy, Financial/Billing) into one page,
 * following the same navigation -> date range -> Generate -> Export flow
 * the use case describes for both. Export is CSV only for now (Table 37
 * allows "PDF or CSV" — CSV covers the requirement; PDF export can be
 * added later the same way the Demand Letter/Eviction Notice PDFs were,
 * via dompdf, if the team decides it's worth the extra time).
 *
 * Scope simplification (flagged for BAGUI): Table 37's Occupancy Report
 * flow mentions "reserved visitor" / "reserved applicant" / "approved /
 * pending move-in payment" as separate bed statuses, and a "historical
 * trend if applicable." Neither exists in the current schema — beds only
 * ever have 4 real statuses (vacant, reserved, occupied, maintenance; see
 * VacancyController::BED_STATUSES), and nothing logs bed-status changes
 * over time. So Occupancy here is always a CURRENT, live snapshot using
 * the real 4 statuses, not a per-period historical view. The Financial
 * report, unlike Occupancy, genuinely does use the date range — payments
 * and penalties are real timestamped records.
 */
class ReportController extends Controller
{
    /**
     * GET /reports — renders the page shell. All actual data loads via
     * the two JSON endpoints below once the admin picks a report type
     * and clicks Generate (same on-demand-data pattern as every other
     * admin page in this app).
     */
    public function page(Request $request)
    {
        return view('adminreports');
    }

    /**
     * GET /reports/occupancy (JSON) — Table 37, [Occupancy Report] branch.
     */
    public function occupancy(Request $request): JsonResponse
    {
        return response()->json($this->computeOccupancy());
    }

    /**
     * GET /reports/financial (JSON) — Table 37, [Financial/Billing Report] branch.
     */
    public function financial(Request $request): JsonResponse
    {
        [$start, $end] = $this->resolveRange($request);

        return response()->json($this->computeFinancial($start, $end));
    }

    /**
     * GET /reports/export?type=occupancy|financial&start=&end=
     * Table 37, step 4 — "Click Export -> generate and download the
     * report as a PDF or CSV." CSV only, per the scope note above.
     *
     * A UTF-8 BOM is written first (see writeCsv() below) — without it,
     * Excel assumes ANSI/Windows-1252 and mangles any non-ASCII character
     * (the — em dash in the title, the ₱ peso sign) into garbage like
     * "â€"". CSV has no built-in way to declare its own encoding, so the
     * BOM is the standard, Excel-recognized way to say "this file is
     * UTF-8" before any real content starts.
     */
    public function export(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['occupancy', 'financial'])],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        if ($data['type'] === 'occupancy') {
            $report = $this->computeOccupancy();
            $filename = 'occupancy-report-' . now()->format('Y-m-d_His') . '.csv';

            $rows = [];
            $rows[] = ['NEST.PH - Occupancy Report'];
            $rows[] = ['Generated', $report['generated_at']];
            $rows[] = [];
            $rows[] = ['Summary'];
            $rows[] = ['Total Rooms', $report['total_rooms']];
            $rows[] = ['Total Bedspaces', $report['total_beds']];
            $rows[] = ['Occupied', $report['occupied']];
            $rows[] = ['Vacant / Available', $report['vacant']];
            $rows[] = ['Reserved', $report['reserved']];
            $rows[] = ['Under Maintenance', $report['maintenance']];
            $rows[] = ['Occupancy Rate', $report['occupancy_rate'] . '%'];
            $rows[] = [];
            $rows[] = ['Breakdown by Floor'];
            $rows[] = ['Floor', 'Total Beds', 'Occupied', 'Vacant', 'Reserved', 'Maintenance', 'Occupancy Rate'];
            foreach ($report['by_floor'] as $floor) {
                $rows[] = [
                    $floor['label'],
                    $floor['total_beds'],
                    $floor['occupied'],
                    $floor['vacant'],
                    $floor['reserved'],
                    $floor['maintenance'],
                    $floor['occupancy_rate'] . '%',
                ];
            }
        } else {
            [$start, $end] = $this->resolveRange($request);
            $report = $this->computeFinancial($start, $end);
            $filename = 'financial-report-' . now()->format('Y-m-d_His') . '.csv';

            $rows = [];
            $rows[] = ['NEST.PH - Financial / Billing Report'];
            $rows[] = ['Period', $report['range']['start'] . ' to ' . $report['range']['end']];
            $rows[] = [];
            $rows[] = ['Metric', 'Amount (PHP)'];
            $rows[] = ['Total Collected', $this->peso($report['total_collected'])];
            $rows[] = ['Cash', $this->peso($report['cash_collected'])];
            $rows[] = ['Online (GCash / Bank / Other)', $this->peso($report['online_collected'])];
            $rows[] = ['Total Outstanding', $this->peso($report['total_outstanding'])];
            $rows[] = ['Total Penalties Applied', $this->peso($report['total_penalties'])];
            $rows[] = [];
            $rows[] = ['Delinquent Accounts (count)', $report['delinquent_accounts']];
            $rows[] = ['Payments Recorded (count)', $report['payment_count']];
        }

        return $this->streamCsv($rows, $filename);
    }

    /**
     * Streams the given rows as a CSV download. Writes a UTF-8 BOM first
     * (see export()'s docblock for why), then every row via fputcsv.
     */
    private function streamCsv(array $rows, string $filename)
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Formats a peso amount for display in the CSV — e.g. 22500 becomes
     * "22,500.00". Plain text with a comma thousands separator, not a raw
     * number, since this export is meant to be read, not recalculated in
     * a spreadsheet formula.
     */
    private function peso(float $amount): string
    {
        return number_format($amount, 2);
    }

    /**
     * Reads ?start= and ?end= (YYYY-MM-DD). Defaults to "this month so
     * far" when not supplied, same default window as the Admin
     * Dashboard's "Revenue (this month)" card.
     */
    private function resolveRange(Request $request): array
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        $start = isset($data['start']) ? Carbon::parse($data['start'])->startOfDay() : now()->startOfMonth();
        $end = isset($data['end']) ? Carbon::parse($data['end'])->endOfDay() : now()->endOfDay();

        return [$start, $end];
    }

    private function computeOccupancy(): array
    {
        $beds = Bed::all();
        $totalBeds = $beds->count();
        $occupied = $beds->where('status', 'occupied')->count();
        $vacant = $beds->where('status', 'vacant')->count();
        $reserved = $beds->where('status', 'reserved')->count();
        $maintenance = $beds->where('status', 'maintenance')->count();

        $byFloor = Floor::with('rooms.beds')->orderBy('floor_number')->get()
            ->map(function (Floor $floor) {
                $floorBeds = $floor->rooms->flatMap->beds;
                $floorTotal = $floorBeds->count();
                $floorOccupied = $floorBeds->where('status', 'occupied')->count();

                return [
                    'label' => 'Floor ' . $floor->floor_number,
                    'total_beds' => $floorTotal,
                    'occupied' => $floorOccupied,
                    'vacant' => $floorBeds->where('status', 'vacant')->count(),
                    'reserved' => $floorBeds->where('status', 'reserved')->count(),
                    'maintenance' => $floorBeds->where('status', 'maintenance')->count(),
                    'occupancy_rate' => $floorTotal > 0 ? round(($floorOccupied / $floorTotal) * 100, 1) : 0,
                ];
            })->values();

        return [
            'generated_at' => now()->format('M j, Y g:ia'),
            'total_rooms' => Room::count(),
            'total_beds' => $totalBeds,
            'occupied' => $occupied,
            'vacant' => $vacant,
            'reserved' => $reserved,
            'maintenance' => $maintenance,
            'occupancy_rate' => $totalBeds > 0 ? round(($occupied / $totalBeds) * 100, 1) : 0,
            'by_floor' => $byFloor,
        ];
    }

    private function computeFinancial(Carbon $start, Carbon $end): array
    {
        $payments = Payment::where('status', 'approved')
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $totalCollected = (float) $payments->sum('amount_paid');
        $cashCollected = (float) $payments->where('payment_method', 'cash')->sum('amount_paid');
        $onlineCollected = round($totalCollected - $cashCollected, 2);

        $totalPenalties = (float) Penalty::whereBetween('date_incurred', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Keeps "overdue" accurate before reading it, same as every other
        // admin page that reads billing_statements.status.
        BillingStatement::syncOverdueStatuses();

        $outstandingBills = BillingStatement::whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->with(['payments' => fn ($q) => $q->where('status', 'approved')])
            ->get();

        $totalOutstanding = $outstandingBills->sum(function (BillingStatement $bill) {
            $paid = (float) $bill->payments->sum('amount_paid');

            return max(0, (float) $bill->total_amount - $paid);
        });

        $delinquentAccounts = BillingStatement::where('status', 'overdue')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->distinct('tenant_id')
            ->count('tenant_id');

        return [
            'range' => [
                'start' => $start->format('M j, Y'),
                'end' => $end->format('M j, Y'),
            ],
            'total_collected' => round($totalCollected, 2),
            'cash_collected' => round($cashCollected, 2),
            'online_collected' => $onlineCollected,
            'total_outstanding' => round($totalOutstanding, 2),
            'total_penalties' => round($totalPenalties, 2),
            'delinquent_accounts' => $delinquentAccounts,
            'payment_count' => $payments->count(),
        ];
    }
}