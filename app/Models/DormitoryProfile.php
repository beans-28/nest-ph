<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryProfile extends Model
{
    protected $table = 'dormitory_profile';

    protected $fillable = [
        'dorm_name',
        'description',
        'address',
        'contact_number',
        'contact_email',
        'logo_path',
        'hero_photo_paths',
        'brand_logo_path',
        'policies_file_path',
        'contract_template_path',
        'business_permit_path',
        'bir_registration_path',
        'gcash_number',
        'bdo_account_number',
        'payments_and_fees',
        'house_rules',
        'checkout_procedures',

        // Lessor details printed on the Tenant Agreement and letterhead.
        'representative_name',
        'representative_position',
        'facebook_page_name',
        'facebook_url',
        'website_url',

        // Rental policy -- billing reads these, and the signed documents
        // print them, so the two can never disagree.
        'rent_due_day',
        'grace_period_days',
        'late_penalty_percent',
        'minimum_stay_months',
        'move_out_notice_days',
        'extension_notice_days',
        'deposit_refund_days',
        'reservation_validity_days',
        'mid_month_move_in',
        'water_included',
        'electricity_included',
        'wifi_included',
        'short_term_rate',
        'transient_rate',
        'rules_version',
        'fees_version',
        'documents_effective_date',
    ];

    /**
     * Same defaults as the migration, so an unsaved profile (no row yet)
     * still behaves like the documents: due on the 1st, 3-day grace, 10%
     * late penalty, 3-month minimum stay.
     */
    protected $attributes = [
        'rent_due_day' => 1,
        'grace_period_days' => 3,
        'late_penalty_percent' => 10,
        'minimum_stay_months' => 3,
        'move_out_notice_days' => 14,
        'extension_notice_days' => 30,
        'deposit_refund_days' => 21,
        'reservation_validity_days' => 30,
        'mid_month_move_in' => 'full',
        'water_included' => false,
        'electricity_included' => false,
        'wifi_included' => false,
    ];

    protected $casts = [
        'hero_photo_paths' => 'array',
        'rent_due_day' => 'integer',
        'grace_period_days' => 'integer',
        'late_penalty_percent' => 'float',
        'minimum_stay_months' => 'integer',
        'move_out_notice_days' => 'integer',
        'extension_notice_days' => 'integer',
        'deposit_refund_days' => 'integer',
        'reservation_validity_days' => 'integer',
        'water_included' => 'boolean',
        'electricity_included' => 'boolean',
        'wifi_included' => 'boolean',
        'short_term_rate' => 'decimal:2',
        'transient_rate' => 'decimal:2',
        'documents_effective_date' => 'date',
    ];

    /**
     * Agreement Section 2.2: with no End Date, the stay ends on the last day
     * of the third month counted from the Start Date. E.g. a 3-month minimum
     * from Oct 20 runs to Jan 19, so it ends Jan 31; from Oct 1 it ends Dec 31.
     */
    public function minimumEndDate(\Carbon\CarbonInterface $start): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($start)
            ->addMonthsNoOverflow(max(1, (int) $this->minimum_stay_months))
            ->subDay()
            ->endOfMonth()
            ->startOfDay();
    }

    /**
     * This system manages exactly one dormitory, so the profile is effectively
     * a singleton — always row 1. Returns an empty (unsaved) instance if no
     * row exists yet, so callers never have to null-check.
     */
    public static function current(): self
    {
        return static::first() ?? new static([
            'dorm_name' => 'NEST.PH',
        ]);
    }

    /**
     * Public URL of the dorm's own logo, or null when none was uploaded
     * (callers then fall back to the NEST.PH mark).
     */
    public function brandLogoUrl(): ?string
    {
        return $this->brand_logo_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->brand_logo_path)
            : null;
    }

    /**
     * Use Case Report — Manage Dormitory Profile (Table 38): uploading BIR
     * Registration credentials is what triggers the public "BIR Registration
     * Seal/Badge" per RMC No. 038-2026. There's no separate boolean column
     * for this on purpose — the presence of the file IS the flag, so it can
     * never drift out of sync with whether a document is actually on file.
     */
    public function isBirVerified(): bool
    {
        return (bool) $this->bir_registration_path;
    }

    /** Cover photo + extra slideshow photos: at most this many in total. */
    public const MAX_HERO_PHOTOS = 5;

    /**
     * Landing page hero slideshow: the cover photo first, then the extra
     * homepage photos. One photo = a still image; 2+ = a carousel.
     */
    public function heroPhotoUrls(): array
    {
        $paths = array_filter(array_merge([$this->logo_path], $this->hero_photo_paths ?? []));

        return array_values(array_map(
            fn ($p) => \Illuminate\Support\Facades\Storage::disk('public')->url($p),
            array_slice($paths, 0, self::MAX_HERO_PHOTOS)
        ));
    }
}
