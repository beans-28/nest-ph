<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\DormitoryProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * The dorm's three documents -- Dormitory Tenant Agreement, Dormitory Rules
 * and Regulations, Payments and Fees Schedule -- filled in for one applicant.
 *
 * The text and design are copied exactly from the dorm's own Word files
 * (PSD_Contract.docx, PSD_Rules & Regulations.docx, PSD_Payments and
 * Fees.docx): same wording, same letterhead (resources/documents/
 * letterhead.jpg), same fonts, sizes and spacing. Only the blanks are filled
 * in: the applicant's details, room, dates, rent and signatures. To change
 * the wording, edit resources/views/documents/*.blade.php.
 *
 * Used for: the documents applicants read on the Apply page, the signed PDF
 * saved with each application, and the blank copies on the public site.
 */
class TenancyDocuments
{
    public const DOCUMENTS = [
        'agreement' => 'Dormitory Tenant Agreement',
        'rules' => 'Dormitory Rules and Regulations',
        'fees' => 'Payments and Fees Schedule',
    ];

    /**
     * Everything the document templates need.
     *
     * $applicant keys (all optional, so blank copies work too): first_name,
     * last_name, home_address, contact_number, email, emergency_contact_name,
     * emergency_contact_relation, emergency_contact_number,
     * preferred_start_date, tenant_end_date, emergency_billing_consent.
     *
     * $signatures keys: tenant, emergency_contact (PNG data URLs), signed_at.
     */
    public function data(array $applicant = [], ?Bed $bed = null, array $signatures = []): array
    {
        $profile = DormitoryProfile::current();
        $bed?->loadMissing('room.roomType');
        $room = $bed?->room;

        $start = ! empty($applicant['preferred_start_date']) ? Carbon::parse($applicant['preferred_start_date']) : null;
        $end = ! empty($applicant['tenant_end_date']) ? Carbon::parse($applicant['tenant_end_date']) : null;
        // Agreement 2.2: no End Date means the last day of the third month.
        if ($start && ! $end) {
            $end = $profile->minimumEndDate($start);
        }

        $signedAt = $signatures['signed_at'] ?? null;

        return [
            'officeAddress' => $profile->address,
            'representative' => trim(collect([$profile->representative_name, $profile->representative_position])->filter()->implode(', ')),

            'tenantName' => trim(($applicant['first_name'] ?? '') . ' ' . ($applicant['last_name'] ?? '')),
            'homeAddress' => $applicant['home_address'] ?? null,
            'mobile' => $applicant['contact_number'] ?? null,
            'email' => $applicant['email'] ?? null,
            'emergencyName' => $applicant['emergency_contact_name'] ?? null,
            'emergencyConsent' => (bool) ($applicant['emergency_billing_consent'] ?? false),

            'bedNo' => $bed?->bed_label,
            'roomNo' => $room?->room_no,
            'roomTypeBox' => $room ? self::roomTypeBox($room) : null,
            'monthlyRent' => $room ? $room->perBedRate() : null,
            'startDate' => $start?->format('F j, Y'),
            'endDate' => $end?->format('F j, Y'),

            'signedAt' => $signedAt ? Carbon::parse($signedAt) : null,
            // "Entered into on the __ day of __": the signing date, or today
            // while an applicant is previewing (they sign the same day).
            // Blank copies (no applicant) keep the blanks.
            'agreementDate' => $signedAt
                ? Carbon::parse($signedAt)
                : (filled($applicant['first_name'] ?? null) ? now() : null),
            'tenantSignature' => $signatures['tenant'] ?? null,
            'emergencySignature' => $signatures['emergency_contact'] ?? null,
        ];
    }

    /**
     * The signed (or blank) documents as one PDF. $only limits it to a
     * single document. Page numbers restart at 1 for each document, like
     * the separate Word files.
     */
    public function pdf(array $data, ?string $only = null)
    {
        $documents = $only ? [$only] : array_keys(self::DOCUMENTS);

        // Where each document starts, so its pages can be numbered from 1.
        $starts = [1];
        if (count($documents) > 1) {
            $page = 1;
            foreach (array_slice($documents, 0, -1) as $document) {
                $page += $this->render($data, [$document])->getDomPDF()->getCanvas()->get_page_count();
                $starts[] = $page;
            }
        }

        $pdf = $this->render($data, $documents);

        $pdf->getDomPDF()->getCanvas()->page_script(function (int $pageNumber, int $pageCount, $canvas, $fontMetrics) use ($starts) {
            $offset = max(array_filter($starts, fn ($start) => $start <= $pageNumber)) - 1;
            $font = $fontMetrics->getFont('Arimo') ?? $fontMetrics->getFont('sans-serif');
            $text = (string) ($pageNumber - $offset);
            // Top-right corner of the letterhead, where Word puts it.
            $canvas->text(586 - $fontMetrics->getTextWidth($text, $font, 11), 58, $text, $font, 11, [0.13, 0.13, 0.13]);
        });

        return $pdf;
    }

    private function render(array $data, array $documents)
    {
        $pdf = Pdf::loadView('pdfs.tenancy-documents', $data + ['documents' => $documents, 'isPdf' => true])
            ->setPaper('letter')
            // Embed only the letters actually used, not whole font files
            // (cuts each signed PDF from ~1 MB to a few hundred KB).
            ->setOption('isFontSubsettingEnabled', true);
        $pdf->render();

        return $pdf;
    }

    /** One document as an HTML fragment (used by tests and text checks). */
    public function html(string $document, array $data): string
    {
        return view('documents.' . $document, $data + ['isPdf' => false])->render();
    }

    /**
     * Which of the Agreement's four Room Type boxes to tick: Solo fan,
     * 4-person AC, 6-person AC, 10-16-person AC. Read from the room's type
     * (its capacity and aircon), falling back to the number of beds.
     */
    public static function roomTypeBox(\App\Models\Room $room): ?string
    {
        $type = $room->roomType;
        $capacity = $type?->max_capacity ?? $room->beds()->count();
        $aircon = $type ? $type->has_aircon : $capacity > 1;

        return match (true) {
            ! $aircon && $capacity <= 1 => 'solo',
            $capacity >= 10 => '10-16',
            $capacity >= 6 => '6',
            $capacity >= 2 => '4',
            default => 'solo',
        };
    }
}
