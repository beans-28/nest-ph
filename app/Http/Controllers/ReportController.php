<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DormitoryProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BillingStatement;
use App\Models\Floor;
use App\Models\LeaseContract;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
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
        RecurringExpense::postDue();
        [$start, $end] = $this->resolveRange($request);

        return response()->json($this->computeFinancial($start, $end));
    }

    /**
     * GET /reports/forecast (JSON) — estimates the next 3 months from the
     * last 6 months of real data. See computeForecast for the method.
     */
    public function forecast(Request $request): JsonResponse
    {
        RecurringExpense::postDue();

        return response()->json($this->computeForecast());
    }

    /**
     * GET /reports/daily (JSON) — Daily Collection report: who paid, how
     * much and when, plus the cash on hand after expenses (see computeDaily).
     * Defaults to today when no dates are picked.
     */
    public function daily(Request $request): JsonResponse
    {
        RecurringExpense::postDue();
        [$start, $end] = $this->resolveRange($request, today: true);

        return response()->json($this->computeDaily($start, $end));
    }

    /**
     * GET /reports/export?type=occupancy|financial|forecast|expenses|daily&start=&end=&format=xlsx|pdf
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
            'type' => ['required', Rule::in(['occupancy', 'financial', 'forecast', 'expenses', 'daily'])],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'format' => ['nullable', Rule::in(['xlsx', 'pdf'])],
        ]);

        $type = $data['type'];
        RecurringExpense::postDue();
        if ($type === 'occupancy') {
            $report = $this->computeOccupancy();
        } elseif ($type === 'forecast') {
            $report = $this->computeForecast();
        } elseif ($type === 'expenses') {
            $report = $this->computeExpenses();
        } elseif ($type === 'daily') {
            [$start, $end] = $this->resolveRange($request, today: true);
            $report = $this->computeDaily($start, $end);
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
                'charts' => $this->pdfCharts($type, $report),
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
    private const REPORT_NAMES = [
        'occupancy' => 'Occupancy Report',
        'financial' => 'Financial / Billing Report',
        'forecast' => 'Forecast Report',
        'expenses' => 'Expenses and Profit Report',
        'daily' => 'Daily Collection Report',
    ];

    /**
     * Builds the Excel workbook. Layout, top to bottom: dorm name title,
     * report name, generated/period info, then one or more tables, each
     * with a green section heading and a green header row.
     */
    private function exportXlsx(string $type, array $report, string $dormName, string $filename)
    {
        $book = new Spreadsheet();
        $book->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $reportName = self::REPORT_NAMES[$type];
        $book->getProperties()->setCreator($dormName)->setTitle("{$dormName} - {$reportName}")
            ->setCompany('Generated via NEST.PH');
        $sheet = $book->getActiveSheet();
        $sheet->setTitle(ucfirst($type));
        $lastCol = ['occupancy' => 'G', 'financial' => 'B', 'forecast' => 'N', 'expenses' => 'L', 'daily' => 'F'][$type];

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
        $sheet->setCellValue('A5', in_array($type, ['financial', 'daily'], true) ? 'Period Covered:' : 'Coverage:');
        $sheet->setCellValue('B5', match ($type) {
            'occupancy' => 'Current room and bed status (live snapshot)',
            'forecast' => 'Estimates for ' . $report['range'] . ', based on the last 6 months',
            'expenses' => $report['range'] ?? 'No months recorded yet',
            'daily' => $report['range']['label'],
            default => $report['range']['start'] . ' to ' . $report['range']['end'],
        });
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

            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Occupancy Trend (last 12 months)',
                ['Month', 'Total Beds', 'Occupied', 'Moved In', 'Moved Out', 'Occupancy Rate'],
                collect($report['trend'])->map(fn ($t) => [
                    $t['label'], $t['total_beds'], $t['occupied'], $t['moved_in'], $t['moved_out'], $t['occupancy_rate'] / 100,
                ])->all());
            $sheet->getStyle('F' . ($start + 2) . ':F' . ($row - 2))->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle('B' . ($start + 1) . ':F' . ($row - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Native Excel chart that reads straight from the trend table above.
            $first = $start + 2;
            $last = $row - 2;
            $row = $this->xlsxChart($sheet, $row, 'Occupancy Rate, Last 12 Months', "A{$first}:A{$last}", [
                [DataSeries::TYPE_LINECHART, 'Occupancy Rate', "F{$first}:F{$last}", self::GREEN],
            ], '0%', 'G');
            $row = $this->xlsxChart($sheet, $row, 'Tenants Moved In / Moved Out per Month', "A{$first}:A{$last}", [
                [DataSeries::TYPE_BARCHART, 'Moved In', "D{$first}:D{$last}", '8FB48F'],
                [DataSeries::TYPE_BARCHART, 'Moved Out', "E{$first}:E{$last}", 'C0504D'],
            ], '0', 'G');
        } elseif ($type === 'forecast') {
            $row = $this->xlsxForecast($sheet, $row, $report);
        } elseif ($type === 'expenses') {
            $t = $report['totals'];
            $keys = array_merge(['collected'], array_keys(Expense::CATEGORIES), ['total', 'net']);
            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Profit by Month',
                array_merge(['Month', 'Collected'], array_values(Expense::CATEGORIES), ['Total Expenses', 'Net Profit']),
                collect($report['months'])->map(fn ($m) => array_merge([$m['label']], array_map(fn ($k) => $m[$k], $keys)))->all()
                    ?: [['No months recorded yet.']],
                $report['months'] ? array_merge(['Total'], array_map(fn ($k) => $t[$k], $keys)) : null);
            $first = $start + 2;
            $last = $row - 2;
            $sheet->getStyle("B{$first}:L{$last}")->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            $sheet->getStyle('A' . ($start + 1) . ':L' . ($start + 1))->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($start + 1)->setRowHeight(32);
            foreach ($report['months'] as $i => $m) {
                if ($m['net'] < 0) {
                    $sheet->getStyle('L' . ($first + $i))->getFont()->getColor()->setRGB(self::RED);
                }
            }
            if ($report['months']) {
                // Chart reads the month rows only (not the Total row).
                $lastMonth = $last - 1;
                $row = $this->xlsxChart($sheet, $row, 'Collected vs. Expenses per Month', "A{$first}:A{$lastMonth}", [
                    [DataSeries::TYPE_BARCHART, 'Collected', "B{$first}:B{$lastMonth}", '8FB48F'],
                    [DataSeries::TYPE_BARCHART, 'Total Expenses', "K{$first}:K{$lastMonth}", 'C0504D'],
                    [DataSeries::TYPE_LINECHART, 'Net Profit', "L{$first}:L{$lastMonth}", self::GREEN],
                ], '"₱"#,##0', 'L');
            }

            // Every expense in those months, so each total can be traced back
            // to the actual purchase and its receipt.
            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Itemized Expenses',
                ['Date Paid', 'Description', 'Category', 'Payment Method', 'Amount', 'Receipt'],
                collect($report['items'])->map(fn ($e) => [
                    $e['date_label'], $e['description'], $e['category_label'], $e['payment_method_label'], $e['amount'],
                    $e['receipt_url'] ? 'Attached' : ($e['is_recurring'] ? 'Recurring (no receipt)' : 'None'),
                ])->all() ?: [['No expenses recorded yet.']]);
            $sheet->getStyle('E' . ($start + 2) . ':E' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            foreach ($report['items'] as $i => $e) {
                if ($e['receipt_url']) {
                    $sheet->getCell('F' . ($start + 2 + $i))->getHyperlink()->setUrl($e['receipt_url']);
                    $sheet->getStyle('F' . ($start + 2 + $i))->getFont()->setUnderline(true)->getColor()->setRGB('1F5FAD');
                }
            }
        } elseif ($type === 'daily') {
            $row = $this->xlsxTable($sheet, $row, 'Cash on Hand', ['Description', 'Amount'], [
                ['Total Collections', $report['total_collected']],
                ['   Cash', $report['cash_collected']],
                ['   Online (GCash / Bank / Other)', $report['online_collected']],
                ['Less: Expenses Paid', $report['total_expenses']],
                ['Remaining After Expenses', $report['remaining']],
                ['Cash Collected', $report['cash_collected']],
                ['Less: Expenses Paid in Cash', $report['cash_expenses']],
            ], ['Net Cash for Bank Deposit', $report['cash_for_deposit']]);
            $sheet->getStyle('B' . ($row - 9) . ':B' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            $sheet->getStyle('A' . ($row - 5))->getFont()->setBold(true);
            if ($report['cash_for_deposit'] < 0) {
                $sheet->getStyle('B' . ($row - 2))->getFont()->getColor()->setRGB(self::RED);
            }

            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Payments Received',
                ['Date', 'Tenant', 'Room', 'Payment Method', 'Reference No.', 'Amount'],
                collect($report['payments'])->map(fn ($p) => [
                    $p['date_label'], $p['tenant'], $p['room'], $p['method_label'], $p['reference'] ?? '', $p['amount'],
                ])->all() ?: [['No payments received in this period.']],
                $report['payments'] ? ['Total', '', '', '', '', $report['total_collected']] : null);
            $sheet->getStyle('F' . ($start + 2) . ':F' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);

            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Expenses Paid',
                ['Date', 'Description', 'Category', 'Payment Method', '', 'Amount'],
                collect($report['expenses'])->map(fn ($e) => [
                    $e['date_label'], $e['description'], $e['category_label'], $e['payment_method_label'], '', $e['amount'],
                ])->all() ?: [['No expenses paid in this period.']],
                $report['expenses'] ? ['Total', '', '', '', '', $report['total_expenses']] : null);
            $sheet->getStyle('F' . ($start + 2) . ':F' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);

            if (count($report['days']) > 1) {
                $start = $row;
                $row = $this->xlsxTable($sheet, $row, 'Day by Day',
                    ['Date', 'Cash Collected', 'Online Collected', 'Expenses Paid', 'Cash Expenses', 'Cash for Deposit'],
                    collect($report['days'])->map(fn ($d) => [
                        $d['date_label'], $d['cash_collected'], $d['online_collected'], $d['total_expenses'], $d['cash_expenses'], $d['cash_for_deposit'],
                    ])->all());
                $sheet->getStyle('B' . ($start + 2) . ':F' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            }
        } else {
            $row = $this->xlsxTable($sheet, $row, 'Revenue and Profit', ['Description', 'Amount'], [
                ['Total Collected', $report['total_collected']],
                ['Less: Total Expenses', $report['total_expenses']],
            ], ['Net Profit', $report['net_profit']]);
            $sheet->getStyle('B' . ($row - 4) . ':B' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            if ($report['net_profit'] < 0) {
                $sheet->getStyle('B' . ($row - 2))->getFont()->getColor()->setRGB(self::RED);
            }

            $b = $report['expense_breakdown'];
            $row = $this->xlsxTable($sheet, $row, 'Expenses Breakdown', ['Expense', 'Amount'],
                collect(Expense::CATEGORIES)->map(fn ($label, $key) => [$label, $b[$key]])->values()->all(),
                ['Total Expenses', $report['total_expenses']]);
            $sheet->getStyle('B' . ($row - 2 - count(Expense::CATEGORIES)) . ':B' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);

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

            // Monthly table uses columns A-D; widen C/D like B.
            $start = $row;
            $row = $this->xlsxTable($sheet, $row, 'Monthly Breakdown', ['Month', 'Collected', 'Expenses', 'Net Profit'],
                collect($report['monthly'])->map(fn ($m) => [$m['label'], $m['collected'], $m['expenses'], $m['net']])->all());
            $sheet->getStyle('B' . ($start + 2) . ':D' . ($row - 2))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
            $sheet->getColumnDimension('C')->setWidth(22);
            $sheet->getColumnDimension('D')->setWidth(22);

            $first = $start + 2;
            $last = $row - 2;
            // Start the chart on a fresh printed page so it isn't cut in half.
            $sheet->setBreak('A' . ($row - 1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
            $row = $this->xlsxChart($sheet, $row, 'Collected vs. Expenses per Month', "A{$first}:A{$last}", [
                [DataSeries::TYPE_BARCHART, 'Collected', "B{$first}:B{$last}", '8FB48F'],
                [DataSeries::TYPE_BARCHART, 'Expenses', "C{$first}:C{$last}", 'C0504D'],
                [DataSeries::TYPE_LINECHART, 'Net Profit', "D{$first}:D{$last}", self::GREEN],
            ], '"₱"#,##0', 'E');
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
        if ($type === 'expenses') {
            $sheet->getColumnDimension('A')->setWidth(18);
            $sheet->getColumnDimension('B')->setWidth(34);
            $sheet->getColumnDimension('C')->setWidth(24);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        }
        if ($type === 'financial') {
            $sheet->getColumnDimension('B')->setWidth(22);
        }
        if ($type === 'daily') {
            $sheet->getColumnDimension('A')->setWidth(32);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(22);
        }

        // Clean look on screen, and fit to one page wide when printed
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getHeaderFooter()->setOddFooter('&L&8' . str_replace('&', '&&', $dormName) . ' - ' . $reportName . '&R&8Page &P of &N');

        return response()->streamDownload(function () use ($book) {
            $writer = new Xlsx($book);
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Adds a native Excel chart below $row, built from cell ranges on this
     * sheet (so it updates if someone edits the numbers in Excel). $series
     * is a list of [chart type, name, value range, hex colour]; bar and
     * line series can be mixed, e.g. bars for money in/out plus a profit
     * line. Returns the next free row after the chart.
     */
    private function xlsxChart($sheet, int $row, string $title, string $categoryRange, array $series, string $numberFormat, string $lastCol): int
    {
        $ref = fn (string $range) => "'" . $sheet->getTitle() . "'!" . preg_replace('/([A-Z]+)(\d+)/', '\$$1\$$2', $range);
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $ref($categoryRange), null, 12)];

        $groups = [];
        foreach ($series as [$chartType, $name, $range, $color]) {
            $values = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $ref($range), $numberFormat, 12);
            if ($chartType === DataSeries::TYPE_LINECHART) {
                $values->setLineColorProperties($color);
                $values->setLineWidth(2.25); // in points; PhpSpreadsheet converts to EMU itself
                $values->setPointMarker('circle');
                $values->getMarkerFillColor()->setColorProperties($color);
                $values->getMarkerBorderColor()->setColorProperties($color);
            } else {
                $values->setFillColor($color);
            }
            // The series name must point at a real cell -- an empty reference
            // makes Excel "repair" the file and delete every chart. Tables put
            // the column header in the row right above the first value.
            preg_match('/^([A-Z]+)(\d+):/', $range, $m);
            $headerCell = $m[1] . ($m[2] - 1);
            $groups[$chartType]['labels'][] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $ref($headerCell), null, 1, [$name]);
            $groups[$chartType]['values'][] = $values;
        }

        $dataSeries = [];
        foreach ($groups as $chartType => $g) {
            $ds = new DataSeries(
                $chartType,
                $chartType === DataSeries::TYPE_BARCHART ? DataSeries::GROUPING_CLUSTERED : DataSeries::GROUPING_STANDARD,
                range(0, count($g['values']) - 1),
                $g['labels'],
                array_fill(0, count($g['values']), $categories[0]),
                $g['values'],
            );
            if ($chartType === DataSeries::TYPE_BARCHART) {
                $ds->setPlotDirection(DataSeries::DIRECTION_COL);
            }
            $dataSeries[] = $ds;
        }

        $chart = new Chart(
            'chart' . $row,
            new Title($title),
            count($series) > 1 ? new Legend(Legend::POSITION_BOTTOM, null, false) : null,
            new PlotArea(null, $dataSeries),
        );
        $chart->setTopLeftPosition("A{$row}");
        $chart->setBottomRightPosition($lastCol . ($row + 17));
        $sheet->addChart($chart);

        return $row + 19;
    }

    /**
     * Draws a simple chart as an SVG image for the PDF export (dompdf can't
     * run the Chart.js charts from the web page). Bars and an optional line
     * share one value axis. Returns a data: URI to use as an <img> src.
     *
     * @param  string[]  $labels
     * @param  array  $bars  list of [name, hex colour, values[]]
     * @param  array|null  $line  [name, hex colour, values[]]
     * @param  callable  $fmt  formats an axis value, e.g. 50 -> "50%"
     */
    private function svgChart(array $labels, array $bars, ?array $line, callable $fmt, ?float $fixedMax = null, array $dotted = []): string
    {
        $w = 680;
        $h = 250;
        [$left, $right, $top, $bottom] = [62, 12, 14, 52];
        $plotW = $w - $left - $right;
        $plotH = $h - $top - $bottom;

        $all = array_merge(...array_map(fn ($b) => $b[2], $bars), ...($line ? [$line[2]] : []),
            ...array_map(fn ($d) => array_values(array_filter($d[2], fn ($v) => $v !== null)), $dotted));
        $max = $fixedMax ?? max(1, max($all ?: [1]));
        $min = min(0, min($all ?: [0]));
        // Round the axis to a "nice" step so gridlines land on clean numbers.
        $rawStep = ($max - $min) / 4;
        $mag = 10 ** floor(log10(max($rawStep, 0.0001)));
        $step = collect([1, 2, 2.5, 5, 10])->map(fn ($m) => $m * $mag)->first(fn ($s) => $s >= $rawStep);
        $max = $fixedMax ?? ceil($max / $step) * $step;
        $min = $min < 0 ? floor($min / $step) * $step : 0;
        $y = fn ($v) => $top + $plotH - (($v - $min) / ($max - $min)) * $plotH;
        $e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES);

        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"{$h}\" viewBox=\"0 0 {$w} {$h}\" font-family=\"DejaVu Sans, sans-serif\">";
        for ($v = $min; $v <= $max + 0.0001; $v += $step) {
            $gy = round($y($v), 1);
            $svg .= "<line x1=\"{$left}\" y1=\"{$gy}\" x2=\"" . ($w - $right) . "\" y2=\"{$gy}\" stroke=\"" . ($v == 0 ? '#999999' : '#e3e3e3') . "\" stroke-width=\"1\"/>";
            $svg .= "<text x=\"" . ($left - 6) . "\" y=\"" . ($gy + 3) . "\" font-size=\"9\" fill=\"#666666\" text-anchor=\"end\">" . $e($fmt($v)) . '</text>';
        }

        $n = max(count($labels), 1);
        $slot = $plotW / $n;
        $barW = min(22, ($slot * 0.7) / max(count($bars), 1));
        foreach ($labels as $i => $label) {
            $cx = $left + $slot * ($i + 0.5);
            foreach ($bars as $j => [, $color, $values]) {
                $v = (float) ($values[$i] ?? 0);
                $bx = round($cx - (count($bars) * $barW) / 2 + $j * $barW, 1);
                $y0 = $y(0);
                $y1 = $y($v);
                $svg .= "<rect x=\"{$bx}\" y=\"" . round(min($y0, $y1), 1) . "\" width=\"" . round($barW - 1, 1) . "\" height=\"" . round(abs($y0 - $y1), 1) . "\" fill=\"#{$color}\"/>";
            }
            $svg .= "<text x=\"" . round($cx, 1) . "\" y=\"" . ($h - $bottom + 14) . "\" font-size=\"8.5\" fill=\"#444444\" text-anchor=\"middle\">" . $e($label) . '</text>';
        }

        if ($line) {
            [, $color, $values] = $line;
            $pts = [];
            foreach ($labels as $i => $label) {
                $pts[] = round($left + $slot * ($i + 0.5), 1) . ',' . round($y((float) ($values[$i] ?? 0)), 1);
            }
            $svg .= "<polyline points=\"" . implode(' ', $pts) . "\" fill=\"none\" stroke=\"#{$color}\" stroke-width=\"2.2\"/>";
            foreach ($pts as $p) {
                [$px, $py] = explode(',', $p);
                $svg .= "<circle cx=\"{$px}\" cy=\"{$py}\" r=\"2.8\" fill=\"#{$color}\"/>";
            }
        }

        // Dotted lines skip months with no value (null), e.g. forecasts
        // that only exist for past months.
        foreach ($dotted as [, $color, $values]) {
            $run = [];
            foreach ($labels as $i => $label) {
                $v = $values[$i] ?? null;
                if ($v !== null) {
                    $run[] = round($left + $slot * ($i + 0.5), 1) . ',' . round($y((float) $v), 1);
                }
                if (($v === null || $i === count($labels) - 1) && $run) {
                    $svg .= "<polyline points=\"" . implode(' ', $run) . "\" fill=\"none\" stroke=\"#{$color}\" stroke-width=\"2\" stroke-dasharray=\"3,3\"/>";
                    foreach ($run as $pt) {
                        [$px, $py] = explode(',', $pt);
                        $svg .= "<circle cx=\"{$px}\" cy=\"{$py}\" r=\"2.5\" fill=\"#{$color}\"/>";
                    }
                    $run = [];
                }
            }
        }

        // Legend along the bottom
        $items = array_merge(array_map(fn ($b) => [$b[0], $b[1]], $bars), $line ? [[$line[0], $line[1]]] : [], array_map(fn ($d) => [$d[0], $d[1]], $dotted));
        $lx = $left;
        foreach ($items as [$name, $color]) {
            $svg .= "<rect x=\"{$lx}\" y=\"" . ($h - 17) . "\" width=\"10\" height=\"10\" fill=\"#{$color}\"/>";
            $svg .= "<text x=\"" . ($lx + 14) . "\" y=\"" . ($h - 8) . "\" font-size=\"9\" fill=\"#444444\">" . $e($name) . '</text>';
            $lx += 24 + strlen($name) * 6;
        }
        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /** The PDF chart images for one report (see svgChart). */
    private function pdfCharts(string $type, array $report): array
    {
        if ($type === 'daily') {
            return [];
        }

        if ($type === 'occupancy') {
            $t = collect($report['trend']);
            $labels = $t->pluck('label')->map(fn ($l) => substr($l, 0, 3) . " '" . substr($l, -2))->all();

            return [
                'trend' => $this->svgChart($labels, [], ['Occupancy rate', '194E19', $t->pluck('occupancy_rate')->all()],
                    fn ($v) => round($v) . '%', 100),
                'moves' => $this->svgChart($labels, [
                    ['Moved in', '8FB48F', $t->pluck('moved_in')->all()],
                    ['Moved out', 'C0504D', $t->pluck('moved_out')->all()],
                ], null, fn ($v) => (string) round($v, 1)),
            ];
        }

        if ($type === 'forecast') {
            // Estimated months get a * after the label (explained in the PDF).
            $rows = collect($report['history'])->concat($report['forecast']);
            $labels = $rows->map(fn ($r) => substr($r['label'], 0, 3) . " '" . substr($r['label'], -2) . (isset($r['ending_leases']) ? '*' : ''))->all();
            $peso = fn ($v) => 'PHP ' . (abs($v) >= 1000 ? rtrim(rtrim(number_format($v / 1000, 1), '0'), '.') . 'k' : round($v));

            return [
                'occupancy' => $this->svgChart($labels, [], ['Occupancy rate', '194E19', $rows->pluck('occupancy_rate')->all()],
                    fn ($v) => round($v) . '%', 100, [
                        ['Forecast at the time', 'B7791F', $rows->map(fn ($r) => isset($r['forecast_occupied']) && $report['total_beds'] > 0
                            ? round($r['forecast_occupied'] / $report['total_beds'] * 100, 1) : null)->all()],
                    ]),
                'money' => $this->svgChart($labels, [
                    ['Income', '8FB48F', $rows->pluck('income')->all()],
                    ['Expenses', 'C0504D', $rows->map(fn ($r) => $r['expenses'] ?? 0)->all()],
                ], null, $peso, null, [
                    ['Forecast income', 'B7791F', $rows->map(fn ($r) => $r['forecast_income'] ?? null)->all()],
                    ['Forecast expenses', '7A4FA0', $rows->map(fn ($r) => $r['forecast_expenses'] ?? null)->all()],
                ]),
            ];
        }

        if ($type === 'expenses') {
            $m = collect($report['months']);
            $labels = $m->pluck('label')->map(fn ($l) => substr($l, 0, 3) . " '" . substr($l, -2))->all();
            $peso = fn ($v) => 'PHP ' . (abs($v) >= 1000 ? rtrim(rtrim(number_format($v / 1000, 1), '0'), '.') . 'k' : round($v));

            return $m->isEmpty() ? [] : [
                'profit' => $this->svgChart($labels, [
                    ['Collected', '8FB48F', $m->pluck('collected')->all()],
                    ['Expenses', 'C0504D', $m->pluck('total')->all()],
                ], ['Net profit', '194E19', $m->pluck('net')->all()], $peso),
            ];
        }

        $m = collect($report['monthly']);
        $labels = $m->pluck('label')->map(fn ($l) => substr($l, 0, 3) . " '" . substr($l, -2))->all();
        $peso = fn ($v) => 'PHP ' . (abs($v) >= 1000 ? rtrim(rtrim(number_format($v / 1000, 1), '0'), '.') . 'k' : round($v));

        return [
            'profit' => $this->svgChart($labels, [
                ['Collected', '8FB48F', $m->pluck('collected')->all()],
                ['Expenses', 'C0504D', $m->pluck('expenses')->all()],
            ], ['Net profit', '194E19', $m->pluck('net')->all()], $peso),
        ];
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
     * GET /reports/expenses?month=YYYY-MM (JSON) — everything the Expenses
     * & Profit tab shows for one month: the expenses recorded in it, the
     * month's totals and net profit, the recurring schedules, and the last
     * 12 months for the "Profit by Month" table.
     */
    public function showExpenses(Request $request): JsonResponse
    {
        $data = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);
        $month = isset($data['month']) ? Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth() : now()->startOfMonth();

        // Record any salaries etc. that fell due since the last visit.
        RecurringExpense::postDue();

        $items = Expense::paidBetween($month, $month->copy()->endOfMonth())
            ->orderByDesc('date_paid')->orderByDesc('id')->get();
        $collected = $this->collectedIn($month);
        $total = round((float) $items->sum('amount'), 2);

        return response()->json([
            'month' => $month->format('Y-m'),
            'label' => $month->format('F Y'),
            // Approved payments received in the month, minus the expenses = net profit.
            'collected' => $collected,
            'total' => $total,
            'net' => round($collected - $total, 2),
            'expenses' => $items->map->toPayload()->values(),
            'recurring' => RecurringExpense::orderByDesc('active')->orderBy('next_date')->get()->map->payload()->values(),
            'history' => $this->expenseHistory(),
        ]);
    }

    /**
     * The last 12 months (newest first) that have at least one expense,
     * each with the total per category, collected and net profit.
     */
    private function expenseHistory()
    {
        $from = now()->startOfMonth()->subMonthsNoOverflow(11);

        return Expense::totalsByMonth($from, now())
            ->sortKeysDesc()
            ->map(function (array $totals, string $key) {
                $month = Carbon::createFromFormat('Y-m', $key)->startOfMonth();
                $collected = $this->collectedIn($month);

                return $totals + [
                    'month' => $key,
                    'label' => $month->format('F Y'),
                    'collected' => $collected,
                    'net' => round($collected - $totals['total'], 2),
                ];
            })->values();
    }

    /**
     * Data for the Expenses and Profit export: the same months as the
     * "Profit by Month" table on the page, oldest first (so charts read
     * left to right), a total for every column, and every expense in
     * those months.
     */
    private function computeExpenses(): array
    {
        $months = $this->expenseHistory()->reverse()->values()->all();
        $totals = [];
        foreach (array_merge(['collected'], array_keys(Expense::CATEGORIES), ['total', 'net']) as $k) {
            $totals[$k] = round(array_sum(array_map(fn ($m) => (float) $m[$k], $months)), 2);
        }

        $items = $months
            ? Expense::paidBetween(
                Carbon::createFromFormat('Y-m', $months[0]['month'])->startOfMonth(),
                Carbon::createFromFormat('Y-m', end($months)['month'])->endOfMonth(),
            )->orderBy('date_paid')->orderBy('id')->get()->map->toPayload()->all()
            : [];

        return [
            'months' => $months,
            'totals' => $totals,
            'items' => $items,
            'range' => $months ? $months[0]['label'] . ' to ' . end($months)['label'] : null,
        ];
    }

    /** Total approved payments dated within the given month. */
    private function collectedIn(Carbon $month): float
    {
        return round((float) Payment::where('status', 'approved')
            ->whereBetween('payment_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->sum('amount_paid'), 2);
    }

    /** Validation shared by "record expense" and "edit expense". */
    private function expenseRules(): array
    {
        return [
            'category' => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'date_paid' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            // Photo of the receipt or a PDF, up to 5 MB.
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * POST /reports/expenses (multipart form) — records one expense, with an
     * optional receipt. With "repeat" on, it also starts a recurring
     * schedule: this expense is the first one, and the next ones are
     * recorded automatically (RecurringExpense::postDue).
     */
    public function storeExpense(Request $request): JsonResponse
    {
        $data = $request->validate($this->expenseRules() + [
            'repeat' => ['nullable', 'boolean'],
            'frequency' => ['required_if:repeat,1,true', 'nullable', Rule::in(array_keys(RecurringExpense::FREQUENCIES))],
        ], [
            'date_paid.before_or_equal' => 'The date paid can\'t be in the future.',
            'receipt.max' => 'The receipt must be 5 MB or smaller.',
            'receipt.mimes' => 'The receipt must be a JPG, PNG or PDF file.',
        ]);

        $datePaid = Carbon::parse($data['date_paid'])->startOfDay();
        $schedule = null;
        if ($request->boolean('repeat')) {
            $schedule = RecurringExpense::create([
                'category' => $data['category'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'frequency' => $data['frequency'],
                'day_of_month' => $data['frequency'] === 'monthly' ? $datePaid->day : null,
                'next_date' => RecurringExpense::nextAfter($datePaid, $data['frequency'], $datePaid->day)->toDateString(),
                'created_by' => $request->user()?->id,
            ]);
        }

        $expense = Expense::create([
            'category' => $data['category'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'date_paid' => $datePaid->toDateString(),
            'payment_method' => $data['payment_method'],
            'receipt_path' => $request->hasFile('receipt') ? $request->file('receipt')->store('expense-receipts', 'public') : null,
            'recurring_expense_id' => $schedule?->id,
            'recorded_by' => $request->user()?->id,
        ]);
        // A back-dated repeating expense may already have later dates due.
        if ($schedule) {
            RecurringExpense::postDue();
        }

        return response()->json([
            'message' => $schedule
                ? 'Expense saved. It will repeat ' . strtolower(RecurringExpense::FREQUENCIES[$schedule->frequency]) . ', next on ' . $schedule->fresh()->next_date->format('M j, Y') . '.'
                : 'Expense saved.',
            'expense' => $expense->toPayload(),
        ], 201);
    }

    /**
     * POST /reports/expenses/{expense} (multipart form) — edits an expense.
     * POST rather than PUT because browsers only send files with POST.
     * A new receipt replaces the old file; remove_receipt deletes it.
     */
    public function updateExpense(Request $request, Expense $expense): JsonResponse
    {
        $data = $request->validate($this->expenseRules() + [
            'remove_receipt' => ['nullable', 'boolean'],
        ], [
            'date_paid.before_or_equal' => 'The date paid can\'t be in the future.',
            'receipt.max' => 'The receipt must be 5 MB or smaller.',
            'receipt.mimes' => 'The receipt must be a JPG, PNG or PDF file.',
        ]);

        if ($request->hasFile('receipt') || $request->boolean('remove_receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $expense->receipt_path = $request->hasFile('receipt') ? $request->file('receipt')->store('expense-receipts', 'public') : null;
        }
        $expense->fill(collect($data)->only(['category', 'description', 'amount', 'date_paid', 'payment_method'])->all());
        $expense->save();

        return response()->json(['message' => 'Expense updated.', 'expense' => $expense->toPayload()]);
    }

    /** DELETE /reports/expenses/{expense} — removes the expense and its receipt file. */
    public function destroyExpense(Expense $expense): JsonResponse
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        $expense->delete();

        return response()->json(['message' => 'Expense deleted.']);
    }

    /**
     * PATCH /reports/recurring-expenses/{recurring} — pause or resume a
     * schedule. Resuming never back-fills the paused dates: the next date
     * moves to the first one from today.
     */
    public function toggleRecurring(Request $request, RecurringExpense $recurring): JsonResponse
    {
        $data = $request->validate(['active' => ['required', 'boolean']]);

        if ($data['active'] && ! $recurring->active) {
            $next = $recurring->next_date->copy();
            while ($next->lt(today())) {
                $next = RecurringExpense::nextAfter($next, $recurring->frequency, $recurring->day_of_month ?? $next->day);
            }
            $recurring->next_date = $next;
        }
        $recurring->active = $data['active'];
        $recurring->save();

        return response()->json([
            'message' => $recurring->active ? 'Schedule resumed.' : 'Schedule paused. No new expenses will be recorded until you resume it.',
            'recurring' => $recurring->payload(),
        ]);
    }

    /**
     * DELETE /reports/recurring-expenses/{recurring} — stops the schedule for
     * good. Expenses it already recorded stay (they were really paid).
     */
    public function destroyRecurring(RecurringExpense $recurring): JsonResponse
    {
        $recurring->delete();

        return response()->json(['message' => 'Schedule removed. Expenses it already recorded were kept.']);
    }

    /**
     * Daily Collection report (Pureza revision 2026-11-14): every approved
     * payment received between $start and $end (who paid, how much, when),
     * the expenses paid in the same days, and the cash on hand:
     *
     *   Remaining after expenses  = all collections - all expenses paid
     *   Net cash for bank deposit = cash collected - expenses paid in cash
     *
     * Only cash goes to the bank: GCash and bank transfers are already in an
     * account, and expenses paid by GCash/bank didn't come out of the cash.
     */
    private function computeDaily(Carbon $start, Carbon $end): array
    {
        $payments = Payment::where('status', 'approved')
            ->whereDate('payment_date', '>=', $start->toDateString())
            ->whereDate('payment_date', '<=', $end->toDateString())
            ->with(['tenant.activeContract.bed.room', 'billingStatement.contract.bed.room'])
            ->orderBy('payment_date')->orderBy('id')
            ->get();
        $expenses = Expense::paidBetween($start, $end)
            ->orderBy('date_paid')->orderBy('id')->get();

        $isCash = fn ($method) => $method === 'cash';
        $sumDay = function ($payments, $expenses) use ($isCash) {
            $cashIn = round((float) $payments->filter(fn ($p) => $isCash($p->payment_method))->sum('amount_paid'), 2);
            $allIn = round((float) $payments->sum('amount_paid'), 2);
            $cashOut = round((float) $expenses->filter(fn ($e) => $isCash($e->payment_method))->sum('amount'), 2);
            $allOut = round((float) $expenses->sum('amount'), 2);

            return [
                'total_collected' => $allIn,
                'cash_collected' => $cashIn,
                'online_collected' => round($allIn - $cashIn, 2),
                'total_expenses' => $allOut,
                'cash_expenses' => $cashOut,
                'remaining' => round($allIn - $allOut, 2),
                'cash_for_deposit' => round($cashIn - $cashOut, 2),
            ];
        };

        $days = [];
        for ($d = $start->copy()->startOfDay(); $d->lte($end) && count($days) < 93; $d->addDay()) {
            $key = $d->toDateString();
            $days[] = ['date' => $key, 'date_label' => $d->format('D, M j, Y')] + $sumDay(
                $payments->filter(fn ($p) => $p->payment_date->toDateString() === $key),
                $expenses->filter(fn ($e) => $e->date_paid->toDateString() === $key),
            );
        }

        $methods = ['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'];

        return [
            'range' => [
                'start' => $start->format('M j, Y'),
                'end' => $end->format('M j, Y'),
                'label' => $start->isSameDay($end) ? $start->format('F j, Y') : $start->format('M j, Y') . ' to ' . $end->format('M j, Y'),
                'single_day' => $start->isSameDay($end),
            ],
            'payments' => $payments->map(fn (Payment $p) => [
                'date_label' => $p->payment_date->format('M j, Y'),
                'tenant' => $p->tenant?->full_name ?? 'Unknown tenant',
                // The room on the bill being paid, else the tenant's current room.
                'room' => $p->billingStatement?->contract?->bed?->room?->room_no
                    ?? $p->tenant?->activeContract?->bed?->room?->room_no ?? '—',
                'method_label' => $p->payment_method === 'other' && $p->payment_method_label
                    ? $p->payment_method_label : ($methods[$p->payment_method] ?? 'Other'),
                'reference' => $p->reference_number,
                'amount' => round((float) $p->amount_paid, 2),
            ])->values()->all(),
            'expenses' => $expenses->map->toPayload()->values()->all(),
            'days' => $days,
        ] + $sumDay($payments, $expenses);
    }

    /**
     * Reads ?start= and ?end= (YYYY-MM-DD). Defaults to "this month so
     * far" when not supplied, same default window as the Admin
     * Dashboard's "Revenue (this month)" card.
     */
    private function resolveRange(Request $request, bool $today = false): array
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        // The Daily Collection report defaults to today only.
        $start = isset($data['start']) ? Carbon::parse($data['start'])->startOfDay() : ($today ? now()->startOfDay() : now()->startOfMonth());
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
            'trend' => $this->occupancyTrend(12),
        ];
    }

    /**
     * Occupancy rate at the end of each of the last $months months (the
     * current month uses today). Beds don't keep a history of their
     * status, so this is rebuilt from lease contracts instead: a contract
     * fills its bed from its start_date until the tenant moved out
     * (contract terminated_at, or the tenant's deactivated_at). A contract
     * with neither date is still living there.
     *
     * Tenants who haven't paid move-in fees yet are left out — they
     * haven't actually moved in.
     */
    private function occupancyTrend(int $months): array
    {
        $stays = LeaseContract::query()
            ->join('tenants', 'tenants.id', '=', 'lease_contracts.tenant_id')
            ->where('tenants.status', '!=', 'pending_move_in_payment')
            ->get(['lease_contracts.start_date', 'lease_contracts.terminated_at', 'tenants.deactivated_at'])
            ->map(function ($c) {
                $out = $c->terminated_at ?? $c->deactivated_at;

                return [
                    'in' => Carbon::parse($c->start_date)->startOfDay(),
                    'out' => $out ? Carbon::parse($out)->startOfDay() : null,
                ];
            });
        $bedDates = Bed::pluck('created_at')->map(fn ($d) => $d ? Carbon::parse($d) : null);

        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = now()->startOfMonth()->subMonthsNoOverflow($i);
            $point = $i === 0 ? now()->endOfDay() : $monthStart->copy()->endOfMonth();

            $totalBeds = $bedDates->filter(fn ($d) => ! $d || $d->lte($point))->count();
            $occupied = $stays->filter(fn ($s) => $s['in']->lte($point) && (! $s['out'] || $s['out']->gt($point)))->count();

            $trend[] = [
                'label' => $monthStart->format('M Y'),
                'total_beds' => $totalBeds,
                'occupied' => min($occupied, $totalBeds),
                'occupancy_rate' => $totalBeds > 0 ? round(min($occupied, $totalBeds) / $totalBeds * 100, 1) : 0,
                'moved_in' => $stays->filter(fn ($s) => $s['in']->between($monthStart, $point))->count(),
                'moved_out' => $stays->filter(fn ($s) => $s['out'] && $s['out']->between($monthStart, $point))->count(),
            ];
        }

        return $trend;
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

        $outstandingBills = BillingStatement::owed()
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

        // Expenses paid within the range (by their date paid).
        $expenseRows = Expense::paidBetween($start, $end)
            ->get(['category', 'amount', 'date_paid', 'payment_method']);
        $breakdown = Expense::summarize($expenseRows);
        $totalExpenses = $breakdown['total'];

        // Month-by-month collected vs. expenses, for the chart and export table.
        $monthly = [];
        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end) && count($monthly) < 36) {
            $key = $cursor->format('Y-m');
            $collected = round((float) $payments->filter(
                fn ($p) => Carbon::parse($p->payment_date)->format('Y-m') === $key
            )->sum('amount_paid'), 2);
            $inMonth = $expenseRows->filter(fn (Expense $e) => $e->date_paid->format('Y-m') === $key);
            $spent = round((float) $inMonth->sum('amount'), 2);
            $monthly[] = [
                'label' => $cursor->format('M Y'),
                'collected' => $collected,
                'expenses' => $spent,
                'net' => round($collected - $spent, 2),
                'has_expenses' => $inMonth->isNotEmpty(),
            ];
            $cursor->addMonthNoOverflow();
        }

        return [
            'range' => [
                'start' => $start->format('M j, Y'),
                'end' => $end->format('M j, Y'),
            ],
            'total_expenses' => $totalExpenses,
            'net_profit' => round($totalCollected - $totalExpenses, 2),
            'expense_breakdown' => array_intersect_key($breakdown, Expense::CATEGORIES),
            'months_missing_expenses' => collect($monthly)->where('has_expenses', false)->pluck('label')->values(),
            'monthly' => $monthly,
            'total_collected' => round($totalCollected, 2),
            'cash_collected' => round($cashCollected, 2),
            'online_collected' => $onlineCollected,
            'total_outstanding' => round($totalOutstanding, 2),
            'total_penalties' => round($totalPenalties, 2),
            'delinquent_accounts' => $delinquentAccounts,
            'payment_count' => $payments->count(),
        ];
    }

    /**
     * Writes the forecast tables (actual months, then estimated months) and
     * a chart into the Excel sheet. Returns the next free row.
     */
    private function xlsxForecast($sheet, int $row, array $report): int
    {
        $a = $report['assumptions'];
        $row = $this->xlsxTable($sheet, $row, 'How the Estimates Are Made', ['Assumption', 'Value'], [
            ['Average new move-ins per month', $a['avg_move_ins']],
            ['Average income per occupied bed', $a['income_per_bed']],
            ['Average monthly expenses', $a['expense_months'] ? $a['avg_expenses'] : 'None recorded'],
            ['Months of expenses recorded (of last 6)', $a['expense_months']],
            ['Average monthly utilities (electricity, water, internet)', $a['expense_months'] ? $a['avg_utilities'] : 'None recorded'],
            ['Utilities range (lowest to highest month)', $a['expense_months'] ? '₱' . number_format($a['min_utilities']) . ' to ₱' . number_format($a['max_utilities']) : 'None recorded'],
            ['Past income estimates were off by (average)', $a['income_error_pct'] === null ? 'Not enough history' : $a['income_error_pct'] . '% over ' . $a['compared_months'] . ' month(s)'],
        ]);
        $sheet->getStyle('B' . ($row - 7) . ':B' . ($row - 6))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
        $sheet->getStyle('B' . ($row - 4))->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
        $sheet->getStyle('B' . ($row - 8) . ':B' . ($row - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("A{$row}", 'Rent can\'t change during a tenant\'s current term; a new price only applies to new tenants and renewals (Tenant Agreement, Section 2.2). "Beds on Locked Rent" counts the beds whose rent is fixed by signed contracts in that month.');
        $sheet->mergeCells("A{$row}:N{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(32);
        $row += 2;

        $headers = ['Month', 'Kind', 'Leases Ending', 'Occupied Beds', 'Occupancy Rate', 'Income', 'Forecast Income',
            'Expenses', 'Forecast Expenses', 'Net', 'Forecast Net', 'Expenses Range', 'Beds on Locked Rent', 'Locked Rent'];
        $toRow = function ($r, $kind) {
            $est = $kind === 'Estimate';
            $fIncome = $r['forecast_income'] ?? null;
            $fExpenses = $r['forecast_expenses'] ?? null;

            return [
                $r['label'], $kind, $r['ending_leases'] ?? '', $r['occupied'], $r['occupancy_rate'] / 100,
                $r['income'], $fIncome, $r['expenses'] ?? '—', $fExpenses,
                $r['net'] ?? '—', $fIncome !== null && $fExpenses !== null ? $fIncome - $fExpenses : null,
                $est && $r['expenses'] !== null ? '₱' . number_format($r['expenses_low']) . ' to ₱' . number_format($r['expenses_high']) . ($r['seasonal'] ? ' *' : '') : '',
                $est ? $r['locked_beds'] . ' of ' . $r['occupied'] : '',
                $est ? $r['locked_rent'] : null,
            ];
        };
        $start = $row;
        $rows = collect($report['history'])->map(fn ($r) => $toRow($r, ! empty($r['partial']) ? 'So far' : 'Actual'))
            ->concat(collect($report['forecast'])->map(fn ($r) => $toRow($r, 'Estimate')))->all();
        $row = $this->xlsxTable($sheet, $row, 'Actual and Estimated Months', $headers, $rows);
        $first = $start + 2;
        $last = $row - 2;
        $sheet->getStyle("E{$first}:E{$last}")->getNumberFormat()->setFormatCode('0.0%');
        $sheet->getStyle("F{$first}:K{$last}")->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
        $sheet->getStyle("N{$first}:N{$last}")->getNumberFormat()->setFormatCode(self::PESO_FORMAT);
        $sheet->getStyle("B{$first}:E{$last}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$first}:M{$last}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Estimated rows in italics so they read as guesses, not records.
        $sheet->getStyle('A' . ($last - count($report['forecast']) + 1) . ":N{$last}")->getFont()->setItalic(true);
        $sheet->setCellValue("A{$row}", 'Forecast columns on past months show what this same method would have estimated at the time, to compare with what actually happened. * = also uses the same month last year (utilities change with the season).');
        $sheet->mergeCells("A{$row}:N{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row += 2;

        $sheet->setBreak('A' . ($row - 1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

        return $this->xlsxChart($sheet, $row, 'Income vs. Expenses: actual, estimated, and what was forecast', "A{$first}:A{$last}", [
            [DataSeries::TYPE_BARCHART, 'Income', "F{$first}:F{$last}", '8FB48F'],
            [DataSeries::TYPE_BARCHART, 'Expenses', "H{$first}:H{$last}", 'C0504D'],
            [DataSeries::TYPE_LINECHART, 'Forecast Income', "G{$first}:G{$last}", 'B7791F'],
            [DataSeries::TYPE_LINECHART, 'Forecast Expenses', "I{$first}:I{$last}", '7A4FA0'],
        ], '"₱"#,##0', 'N');
    }

    /**
     * Forecast for the next 3 months, using simple averages of the last 6
     * months (a dorm has too little history for anything fancier, and
     * averages are easy to check by hand). For each future month:
     *
     *   occupied  = last month's occupied
     *               - leases whose end_date falls in that month (assumed to
     *                 move out; some may renew, so this leans cautious)
     *               + average move-ins per month over the last 6 months
     *               (kept between 0 and the total number of beds)
     *   income    = occupied x average income per occupied bed, where that
     *               average is (money actually collected in the past full
     *               months) / (occupied beds summed over those months). This
     *               already includes utilities and late payers, so no
     *               separate collection rate is needed.
     *   expenses  = salaries + other (6-month average) plus utilities.
     *               Utilities go up and down (e.g. electricity in summer), so
     *               if the same month last year was recorded, the estimate
     *               is the average of the 6-month average and last year's
     *               bill, and a low-high range is shown from past months.
     *   net       = income - expenses.
     *
     * Past months also get the estimate this same method would have made
     * at the time ("forecast" vs "actual"), so admins can see how close
     * the method usually is.
     *
     * Rent: by contract a tenant's rent can't change during their current
     * term; a new rate only applies on renewal or to new tenants (Tenant
     * Agreement 2.2, Payments and Fees Schedule). Each future month shows
     * how much rent is locked by signed contracts.
     */
    private function computeForecast(int $ahead = 3, int $lookBack = 6): array
    {
        // Twice the look-back, so the oldest shown month still has 6 months
        // before it to make its "would-have-forecast" from.
        $byMonth = Expense::totalsByMonth(now()->startOfMonth()->subMonthsNoOverflow($lookBack * 2 - 1), now());
        $all = collect($this->occupancyTrend($lookBack * 2))->values()->map(function ($t, $i) use ($lookBack, $byMonth) {
            $month = now()->startOfMonth()->subMonthsNoOverflow($lookBack * 2 - 1 - $i);
            $expense = $byMonth->get($month->format('Y-m'));
            $income = $this->collectedIn($month);

            return [
                'month' => $month,
                'label' => $t['label'],
                'total_beds' => $t['total_beds'],
                'occupied' => $t['occupied'],
                'occupancy_rate' => $t['occupancy_rate'],
                'moved_in' => $t['moved_in'],
                'income' => $income,
                'expenses' => $expense ? $expense['total'] : null,
                'utilities' => $expense ? $expense['utilities'] : null,
                'net' => $expense ? round($income - $expense['total'], 2) : null,
            ];
        });
        $totalBeds = Bed::count();
        // Estimates are rounded to the nearest P100: an exact centavo
        // figure would look more certain than a guess is.
        $round100 = fn ($v) => round($v / 100) * 100;
        $endingIn = fn (Carbon $m) => LeaseContract::whereBetween('end_date', [$m->copy()->startOfMonth()->toDateString(), $m->copy()->endOfMonth()->toDateString()])->count();

        // The averages one estimate is built from. $window = the finished
        // months just before the month being estimated.
        $basis = function ($window) {
            $bedMonths = $window->sum('occupied');
            $recorded = $window->whereNotNull('expenses');

            return [
                'income_per_bed' => $bedMonths > 0 && $window->sum('income') > 0 ? $window->sum('income') / $bedMonths : null,
                'avg_move_ins' => round($window->avg('moved_in') ?? 0, 1),
                'recorded' => $recorded,
                'avg_expenses' => $recorded->count() ? round($recorded->avg('expenses'), 2) : null,
            ];
        };

        // Last 6 months, each with what this method would have estimated
        // for it at the time. The current month isn't over, so it has none.
        $history = collect();
        for ($i = $lookBack; $i < $lookBack * 2; $i++) {
            $row = $all[$i];
            $isCurrent = $i === $lookBack * 2 - 1;
            $past = $basis($all->slice($i - $lookBack, $lookBack));
            $est = null;
            if (! $isCurrent && $past['income_per_bed'] !== null) {
                $occ = (int) round(max(0, min($row['total_beds'], $all[$i - 1]['occupied'] - $endingIn($row['month']) + $past['avg_move_ins'])));
                $est = [
                    'occupied' => $occ,
                    'income' => $round100($occ * $past['income_per_bed']),
                    'expenses' => $past['avg_expenses'] !== null ? $round100($past['avg_expenses']) : null,
                ];
            }
            unset($row['month'], $row['total_beds'], $row['utilities']);
            $history->push($row + [
                'partial' => $isCurrent,
                'forecast_occupied' => $est['occupied'] ?? null,
                'forecast_income' => $est['income'] ?? null,
                'forecast_expenses' => $est['expenses'] ?? null,
            ]);
        }

        // On average, how far off the past income estimates were.
        $compared = $history->filter(fn ($r) => $r['forecast_income'] !== null && $r['income'] > 0);
        $incomeError = $compared->count()
            ? round($compared->avg(fn ($r) => abs($r['forecast_income'] - $r['income']) / $r['income'] * 100), 1)
            : null;

        // The real forecast uses the last 6 finished months (the current
        // month is left out: its income so far would drag the averages down).
        $b = $basis($all->slice($lookBack - 1, $lookBack));
        $incomePerBed = $b['income_per_bed']
            // No history yet: fall back to the average rent on active leases.
            ?? (float) LeaseContract::whereIn('status', ['active', 'expiring_soon'])->avg('monthly_rate');
        $avgMoveIns = $b['avg_move_ins'];
        $recorded = $b['recorded'];
        $avgExpenses = $b['avg_expenses'] ?? 0.0;
        $hasExpenses = $recorded->count() > 0;

        // Split expenses into steady costs (salaries, other) and utilities,
        // which swing from month to month.
        $avgFixed = $hasExpenses ? $recorded->avg(fn ($r) => $r['expenses'] - $r['utilities']) : 0;
        $avgUtilities = $hasExpenses ? $recorded->avg('utilities') : 0;
        $minUtilities = $hasExpenses ? $recorded->min('utilities') : 0;
        $maxUtilities = $hasExpenses ? $recorded->max('utilities') : 0;

        // Start from today's occupied beds minus the leases that still end
        // later this month, so next month doesn't count them as staying.
        $occupied = Bed::where('status', 'occupied')->count()
            - LeaseContract::whereIn('status', ['active', 'expiring_soon'])
                ->whereNull('terminated_at')
                ->whereBetween('end_date', [now()->toDateString(), now()->endOfMonth()->toDateString()])
                ->count();
        $forecast = [];
        for ($i = 1; $i <= $ahead; $i++) {
            $month = now()->startOfMonth()->addMonthsNoOverflow($i);
            $ending = LeaseContract::whereIn('status', ['active', 'expiring_soon'])
                ->whereNull('terminated_at')
                ->whereBetween('end_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
                ->count();
            $occupied = (int) round(max(0, min($totalBeds, $occupied - $ending + $avgMoveIns)));
            $income = $round100($occupied * $incomePerBed);

            // Leases that cover this whole month: their rent is fixed by
            // contract, so a price change can't touch it.
            $locked = LeaseContract::whereIn('status', ['active', 'expiring_soon'])
                ->whereNull('terminated_at')
                ->whereDate('end_date', '>=', $month->copy()->endOfMonth()->toDateString());
            $lockedBeds = (clone $locked)->count();

            $expenses = $low = $high = null;
            $seasonal = false;
            if ($hasExpenses) {
                $utilities = $avgUtilities;
                $monthMin = $minUtilities;
                $monthMax = $maxUtilities;
                // Same month last year recorded? Lean towards it, so summer
                // electricity spikes etc. show up in the estimate.
                $lastYear = Expense::totalsForMonth($month->copy()->subYear());
                if ($lastYear) {
                    $lastYearUtilities = $lastYear['utilities'];
                    $utilities = ($avgUtilities + $lastYearUtilities) / 2;
                    $monthMin = min($monthMin, $lastYearUtilities);
                    $monthMax = max($monthMax, $lastYearUtilities);
                    $seasonal = true;
                }
                $expenses = $round100($avgFixed + $utilities);
                $low = $round100($avgFixed + $monthMin);
                $high = $round100($avgFixed + $monthMax);
            }

            $forecast[] = [
                'label' => $month->format('M Y'),
                'ending_leases' => $ending,
                'occupied' => $occupied,
                'occupancy_rate' => $totalBeds > 0 ? round($occupied / $totalBeds * 100, 1) : 0,
                'income' => $income,
                'expenses' => $expenses,
                'expenses_low' => $low,
                'expenses_high' => $high,
                'seasonal' => $seasonal,
                // No expenses recorded = no honest profit figure.
                'net' => $hasExpenses ? $income - $expenses : null,
                'locked_beds' => $lockedBeds,
                'locked_rent' => round((float) (clone $locked)->sum('monthly_rate'), 2),
                // Beds whose rent follows the current price list (new
                // tenants and renewals).
                'open_beds' => max(0, $occupied - $lockedBeds),
            ];
        }

        return [
            'generated_at' => now()->format('M j, Y g:ia'),
            'range' => $forecast[0]['label'] . ' to ' . end($forecast)['label'],
            'total_beds' => $totalBeds,
            'history' => $history->all(),
            'forecast' => $forecast,
            'totals' => [
                'income' => round(array_sum(array_column($forecast, 'income')), 2),
                'expenses' => $hasExpenses ? array_sum(array_column($forecast, 'expenses')) : null,
                'expenses_low' => $hasExpenses ? array_sum(array_column($forecast, 'expenses_low')) : null,
                'expenses_high' => $hasExpenses ? array_sum(array_column($forecast, 'expenses_high')) : null,
                'net' => $hasExpenses ? array_sum(array_column($forecast, 'net')) : null,
                'ending_leases' => array_sum(array_column($forecast, 'ending_leases')),
            ],
            'assumptions' => [
                'avg_move_ins' => $avgMoveIns,
                'income_per_bed' => round($incomePerBed, 2),
                'avg_expenses' => $avgExpenses,
                'expense_months' => $recorded->count(),
                'avg_utilities' => round($avgUtilities, 2),
                'min_utilities' => round($minUtilities, 2),
                'max_utilities' => round($maxUtilities, 2),
                'income_error_pct' => $incomeError,
                'compared_months' => $compared->count(),
            ],
        ];
    }
}
