<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DormitoryProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BillingStatement;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Use Case Report Table 36 — Generate Reports.
 *
 * Combines both report types (Occupancy, Financial/Billing) into one page,
 * following the same navigation -> date range -> Generate -> Export flow
 * the use case describes for both. Export is available as Excel (.xlsx)
 * or PDF (Table 36 allows "PDF or CSV"; a styled Excel file replaced the
 * plain CSV because CSV cannot hold any formatting); the PDF uses dompdf and is styled
 * like the Demand Letter PDF (see resources/views/pdfs/report.blade.php).
 *
 * Scope simplification (flagged for BAGUI): Table 36's Occupancy Report
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
     * GET /reports/occupancy (JSON) — Table 36, [Occupancy Report] branch.
     */
    public function occupancy(Request $request): JsonResponse
    {
        return response()->json($this->computeOccupancy());
    }

    /**
     * GET /reports/financial (JSON) — Table 36, [Financial/Billing Report] branch.
     */
    public function financial(Request $request): JsonResponse
    {
        [$start, $end] = $this->resolveRange($request);

        return response()->json($this->computeFinancial($start, $end));
    }

    /**
     * GET /reports/export?type=occupancy|financial&start=&end=&format=xlsx|pdf
     * Table 36, step 4 — "Click Export -> generate and download the
     * report." Two formats:
     *   - xlsx (default): a styled Excel workbook (PhpSpreadsheet). This
     *     replaced the old CSV export, since a CSV is plain text and can't
     *     hold column widths, bold headings, colours or number formats.
     *   - pdf: laid out like the Demand Letter PDF (pdfs/report.blade.php).
     */
    public function export(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['occupancy', 'financial'])],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'format' => ['nullable', Rule::in(['xlsx', 'pdf'])],
        ]);

        $type = $data['type'];
        if ($type === 'occupancy') {
            $report = $this->computeOccupancy();
        } else {
            [$start, $end] = $this->resolveRange($request);
            $report = $this->computeFinancial($start, $end);
        }
        $dormName = DormitoryProfile::current()->dorm_name ?? 'NEST PH';
        $filename = "{$type}-report-" . now()->format('Y-m-d_His');

        if (($data['format'] ?? 'xlsx') === 'pdf') {
            $pdf = Pdf::loadView('pdfs.report', [
                'type' => $type,
                'report' => $report,
                'dormName' => $dormName,
            ]);
            // "Page X of Y" under the footer line on every page. dompdf can
            // only know the total page count after rendering, so it's stamped
            // onto the finished pages here rather than written in the template.
            $dompdf = $pdf->getDomPDF();
            $dompdf->render();
            $canvas = $dompdf->getCanvas();
            $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
            $canvas->page_text($canvas->get_width() / 2 - 22, $canvas->get_height() - 22,
                'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 7.5, [0.54, 0.54, 0.54]);

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}.pdf\"",
            ]);
        }

        return $this->exportXlsx($type, $report, $dormName, "{$filename}.xlsx");
    }

    // Colours shared with the PDF / Demand Letter so all exports match.
    private const GREEN = '194E19';
    private const GREEN_LIGHT = 'DCEBDC';
    private const GREY = 'F2F2F2';
    private const RED = 'BA2828';
    private const PESO_FORMAT = '"₱"#,##0.00';

    /**
     * Builds the Excel workbook. Layout, top to bottom: dorm name title,
     * report name, generated/period info, then one or more tables, each
     * with a green section heading and a green header row.
     */
    private function exportXlsx(string $type, array $report, string $dormName, string $filename)
    {
        $book = new Spreadsheet();
        $book->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $reportName = $type === 'occupancy' ? 'Occupancy Report' : 'Financial / Billing Report';
        $book->getProperties()->setCreator($dormName)->setTitle("{$dormName} - {$reportName}")
            ->setCompany('Generated via NEST.PH');
        $sheet = $book->getActiveSheet();
        $sheet->setTitle($type === 'occupancy' ? 'Occupancy' : 'Financial');
        $lastCol = $type === 'occupancy' ? 'G' : 'B';

        // Title block
        $sheet->setCellValue('A1', $dormName);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB(self::GREEN);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->setCellValue('A2', $reportName);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->getFont()->setSize(12)->getColor()->setRGB('4B5F4C');

        $sheet->setCellValue('A4', 'Date Generated:');
        $sheet->setCellValue('B4', now()->format('F j, Y g:i A'));
        $sheet->setCellValue('A5', $type === 'occupancy' ? 'Coverage:' : 'Period Covered:');
        $sheet->setCellValue('B5', $type === 'occupancy'
            ? 'Current room and bed status (live snapshot)'
            : $report['range']['start'] . ' to ' . $report['range']['end']);
        $sheet->getStyle('A4:A5')->getFont()->setBold(true);
        // Let the info text spill across the columns instead of being cut off
        $sheet->getStyle('B4:B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $row = 7;
        if ($type === 'occupancy') {
            $row = $this->xlsxTable($sheet, $row, 'Summary', ['Metric', 'Count'], [
                ['Total Rooms', $report['total_rooms']],
                ['Total Bedspaces', $report['total_beds']],
                ['Occupied', $report['occupied']],
                ['Vacant / Available', $report['vacant']],
                ['Reserved', $report['reserved']],
                ['Under Maintenance', $report['maintenance']],
            ], ['Occupancy Rate', $report['occupancy_rate'] / 100]);
            $sheet->getStyle('B' . ($row - 2))->getNumberFormat()->setFormatCode('0.0%');

            $floors = collect($report['by_floor'])->map(fn ($f) => [
                $f['label'], $f['total_beds'], $f['occupied'], $f['vacant'],
                $f['reserved'], $f['maintenance'], $f['occupancy_rate'] / 100,
            ])->all();
            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Breakdown by Floor',
                ['Floor', 'Total Beds', 'Occupied', 'Vacant', 'Reserved', 'Maintenance', 'Occupancy Rate'],
                $floors ?: [['No floors added yet.']]);
            $sheet->getStyle('G' . ($start + 2) . ':G' . ($row - 2))->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle('B' . ($start + 1) . ':G' . ($row - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $row = $this->xlsxTable($sheet, $row, 'Collections Breakdown', ['Description', 'Amount'], [
                ['Cash', $report['cash_collected']],
                ['Online (GCash / Bank / Other)', $report['online_collected']],
            ], ['Total Collected', $report['total_collected']]);
            $sheet->getStyle('B' . ($row - 4) . ':B' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);

            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Receivables and Delinquency', ['Metric', 'Value'], [
                ['Total Outstanding', $report['total_outstanding']],
                ['Total Penalties Applied', $report['total_penalties']],
                ['Delinquent Accounts', $report['delinquent_accounts']],
                ['Payments Recorded', $report['payment_count']],
            ]);
            // Peso format + red for the two money rows
            $money = 'B' . ($start + 2) . ':B' . ($start + 3);
            $sheet->getStyle($money)->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            $sheet->getStyle($money)->getFont()->setBold(true)->getColor()->setRGB(self::RED);
        }

        // Footer note
        $row += 1;
        $sheet->setCellValue("A{$row}", "Issued by {$dormName} · Generated via NEST.PH Dormitory Management System");
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('8A8A8A');

        // Column widths: wide label column, even widths for the rest
        $sheet->getColumnDimension('A')->setWidth(34);
        foreach (range('B', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setWidth(16);
        }
        if ($type === 'financial') {
            $sheet->getColumnDimension('B')->setWidth(22);
        }

        // Clean look on screen, and fit to one page wide when printed
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getHeaderFooter()->setOddFooter('&L&8' . str_replace('&', '&&', $dormName) . ' - ' . $reportName . '&R&8Page &P of &N');

        return response()->streamDownload(function () use ($book) {
            (new Xlsx($book))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Writes one titled table starting at $row: a green section heading,
     * a green header row, zebra-striped data rows and (optionally) a
     * bold, light-green total row. Returns the next free row, leaving one
     * blank row as a gap before the next table (so the table's last row
     * is the returned row - 2).
     *
     * fromArray()'s 4th argument (strict null check) is true so that a
     * real 0 is written as 0 instead of being treated as empty.
     */
    private function xlsxTable($sheet, int $row, string $title, array $headers, array $rows, ?array $total = null): int
    {
        $lastCol = chr(ord('A') + count($headers) - 1);

        $sheet->setCellValue("A{$row}", strtoupper($title));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(self::GREEN);
        $row++;

        $headerRow = $row;
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::GREEN]],
        ]);

        foreach ($rows as $i => $data) {
            $row++;
            $sheet->fromArray($data, null, "A{$row}", true);
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::GREY);
            }
        }

        if ($total) {
            $row++;
            $sheet->fromArray($total, null, "A{$row}", true);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => self::GREEN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::GREEN_LIGHT]],
            ]);
        }

        if (count($headers) === 2) {
            $sheet->getStyle("B{$headerRow}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$row}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CCCCCC');

        return $row + 2;
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