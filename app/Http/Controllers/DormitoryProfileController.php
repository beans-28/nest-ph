<?php

namespace App\Http\Controllers;

use App\Models\DormitoryAmenity;
use App\Models\DormitoryCharge;
use App\Models\DormitoryHouseRule;
use App\Models\DormitoryProfile;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Review;

/**
 * Use Case Report — Manage Dormitory Profile (Table 38). The Dormitory
 * Administrator/Owner sets up or updates the dormitory's basic info, house
 * rules, amenities, cover photo, policies file, and optional Business
 * Permit / BIR Registration documents. Everything here is read straight
 * back out by the public "Dorm Info" page (PublicController::dormInfoPage).
 */
class DormitoryProfileController extends Controller
{
    /**
     * Renders the admin Dormitory Profile page.
     */
    public function page()
    {
        $profile = DormitoryProfile::current();

        $amenities = DormitoryAmenity::orderBy('sort_order')->get();
        $houseRules = DormitoryHouseRule::orderBy('sort_order')->orderBy('id')->get();

        $reviews = Review::with(['tenant', 'moderator'])->latest()->get();

        return view('admindormitoryprofile', [
            'profile' => $profile,
            'coverPhotoUrl' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'heroPhotos' => $this->heroPhotoList($profile),
            'businessPermitName' => $profile->business_permit_path ? basename($profile->business_permit_path) : null,
            'businessPermitExt' => $profile->business_permit_path ? strtoupper(pathinfo($profile->business_permit_path, PATHINFO_EXTENSION)) : null,
            'businessPermitImageUrl' => $this->isImageFile($profile->business_permit_path)
                ? Storage::disk('public')->url($profile->business_permit_path)
                : null,
            'birRegistrationName' => $profile->bir_registration_path ? basename($profile->bir_registration_path) : null,
            'birRegistrationExt' => $profile->bir_registration_path ? strtoupper(pathinfo($profile->bir_registration_path, PATHINFO_EXTENSION)) : null,
            'birRegistrationImageUrl' => $this->isImageFile($profile->bir_registration_path)
                ? Storage::disk('public')->url($profile->bir_registration_path)
                : null,
            'amenities' => $amenities,
            'houseRules' => $houseRules,
            'paymentMethods' => \App\Models\PaymentMethod::ordered()->map->toClientArray()->values(),
            'roomTypes' => RoomType::withCount('rooms')->orderBy('sort_order')->orderBy('id')->get()->map->toClientArray()->values(),
            'charges' => DormitoryCharge::ordered()->map->toClientArray()->values(),
            'reviews' => $reviews,
            'reviewCounts' => [
                'all' => $reviews->count(),
                'published' => $reviews->where('status', 'published')->count(),
                'hidden' => $reviews->where('status', 'hidden')->count(),
                'removed' => $reviews->where('status', 'removed')->count(),
            ],
            'reviewAverage' => Review::aggregate()['average'],
        ]);
    }

    /**
     * Step 2 of the use case — edit the basic dormitory details. Text fields
     * only; every file upload has its own endpoint below so a slow image
     * upload can't block saving a one-word description typo fix, and vice
     * versa.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dorm_name' => ['required', 'string', 'max:150'],
            'contact_number' => ['required', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $profile = DormitoryProfile::current();

        if (! $profile->exists) {
            $profile->fill($data)->save();
        } else {
            $profile->update($data);
        }

        return response()->json([
            'message' => 'Dormitory profile updated successfully.',
            'profile' => $profile->fresh(),
        ]);
    }

    /**
     * "Change Cover Photo" / "Upload New Cover". Reuses the existing
     * logo_path column — this project only has one cover image slot, and
     * that column was never actually used anywhere else, so no new column
     * was needed for it.
     */
    public function uploadCoverPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'cover_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        if ($profile->logo_path && Storage::disk('public')->exists($profile->logo_path)) {
            Storage::disk('public')->delete($profile->logo_path);
        }

        $path = $request->file('cover_photo')->store('dormitory-profile', 'public');
        $profile->update(['logo_path' => $path]);

        return response()->json([
            'message' => 'Cover photo updated.',
            'cover_photo_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Extra homepage slideshow photos (shown after the cover photo). The
     * cover counts toward the 5-photo limit, so at most 4 extras.
     */
    public function uploadHeroPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        $paths = $profile->hero_photo_paths ?? [];
        if (count($paths) >= DormitoryProfile::MAX_HERO_PHOTOS - 1) {
            return response()->json([
                'message' => 'You can have up to ' . DormitoryProfile::MAX_HERO_PHOTOS . ' homepage photos, including the cover photo. Remove one first.',
            ], 422);
        }

        $paths[] = $request->file('photo')->store('dormitory-profile/hero', 'public');
        $profile->update(['hero_photo_paths' => $paths]);

        return response()->json([
            'message' => 'Homepage photo added.',
            'photos' => $this->heroPhotoList($profile),
        ]);
    }

    /**
     * Removes by file name, not list position, so an old tab or two quick
     * clicks can't remove the wrong photo.
     */
    public function deleteHeroPhoto(string $name): JsonResponse
    {
        $profile = DormitoryProfile::current();
        $paths = $profile->hero_photo_paths ?? [];
        $index = array_search($name, array_map('basename', $paths), true);

        if ($index === false) {
            return response()->json([
                'message' => 'That photo was already removed.',
                'photos' => $this->heroPhotoList($profile),
            ], 404);
        }

        Storage::disk('public')->delete($paths[$index]);
        array_splice($paths, $index, 1);
        $profile->update(['hero_photo_paths' => $paths]);

        return response()->json([
            'message' => 'Homepage photo removed.',
            'photos' => $this->heroPhotoList($profile),
        ]);
    }

    private function heroPhotoList(DormitoryProfile $profile): array
    {
        return array_map(
            fn ($p) => ['url' => Storage::disk('public')->url($p), 'name' => basename($p)],
            $profile->hero_photo_paths ?? []
        );
    }

    /**
     * "Dorm Logo" — the dorm's own square logo, shown in the sidebar, login
     * page, browser tab and emails, with "Powered by NEST.PH" alongside it.
     * Separate from the cover photo because a wide banner can't shrink into
     * a small icon.
     */
    public function uploadBrandLogo(Request $request): JsonResponse
    {
        $request->validate([
            'brand_logo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        if ($profile->brand_logo_path && Storage::disk('public')->exists($profile->brand_logo_path)) {
            Storage::disk('public')->delete($profile->brand_logo_path);
        }

        $path = $request->file('brand_logo')->store('dormitory-profile', 'public');
        $profile->update(['brand_logo_path' => $path]);

        return response()->json([
            'message' => 'Dorm logo updated.',
            'brand_logo_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Blank copy of one of the dorm's documents (agreement / rules / fees),
     * for the admin to check. The old "policies file" upload was replaced:
     * the public Dorm Info page now always shows the Rules and Regulations.
     */
    public function previewDocument(string $document, \App\Services\TenancyDocuments $documents)
    {
        return $documents->pdf($documents->data(), $document)
            ->stream(\Illuminate\Support\Str::slug(\App\Services\TenancyDocuments::DOCUMENTS[$document]) . '.pdf');
    }

    /**
     * Step 4 (extend use case) — optional Business Permit upload.
     */
    public function uploadBusinessPermit(Request $request): JsonResponse
    {
        $request->validate([
            'business_permit' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        if ($profile->business_permit_path && Storage::disk('public')->exists($profile->business_permit_path)) {
            Storage::disk('public')->delete($profile->business_permit_path);
        }

        $path = $request->file('business_permit')->store('dormitory-profile/legitimacy', 'public');
        $profile->update(['business_permit_path' => $path]);

        return response()->json([
            'message' => 'Business Permit uploaded.',
            'file_name' => basename($path),
            'file_ext' => strtoupper(pathinfo($path, PATHINFO_EXTENSION)),
            'image_url' => $this->isImageFile($path) ? Storage::disk('public')->url($path) : null,
        ]);
    }

    /**
     * Step 7 (extend use case) — remove the uploaded Business Permit.
     */
    public function deleteBusinessPermit(): JsonResponse
    {
        $profile = DormitoryProfile::current();

        if ($profile->business_permit_path && Storage::disk('public')->exists($profile->business_permit_path)) {
            Storage::disk('public')->delete($profile->business_permit_path);
        }

        $profile->update(['business_permit_path' => null]);

        return response()->json(['message' => 'Business Permit removed.']);
    }

    /**
     * Step 5 (extend use case) — optional BIR Registration upload. This is
     * the one that turns the public "BIR Registration Seal/Badge" on.
     */
    public function uploadBirRegistration(Request $request): JsonResponse
    {
        $request->validate([
            'bir_registration' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        if ($profile->bir_registration_path && Storage::disk('public')->exists($profile->bir_registration_path)) {
            Storage::disk('public')->delete($profile->bir_registration_path);
        }

        $path = $request->file('bir_registration')->store('dormitory-profile/legitimacy', 'public');
        $profile->update(['bir_registration_path' => $path]);

        return response()->json([
            'message' => 'BIR Registration uploaded. The verification badge now appears on your public profile.',
            'file_name' => basename($path),
            'file_ext' => strtoupper(pathinfo($path, PATHINFO_EXTENSION)),
            'image_url' => $this->isImageFile($path) ? Storage::disk('public')->url($path) : null,
        ]);
    }

    /**
     * Step 7 (extend use case) — remove the uploaded BIR Registration. This
     * also turns the public badge back off, since presence of the file is
     * the only thing that badge checks.
     */
    public function deleteBirRegistration(): JsonResponse
    {
        $profile = DormitoryProfile::current();

        if ($profile->bir_registration_path && Storage::disk('public')->exists($profile->bir_registration_path)) {
            Storage::disk('public')->delete($profile->bir_registration_path);
        }

        $profile->update(['bir_registration_path' => null]);

        return response()->json(['message' => 'BIR Registration removed. The verification badge no longer appears on your public profile.']);
    }

    /**
     * Amenities checklist — toggle a single amenity on/off. The list of
     * possible amenities itself is fixed (seeded by migration), so this
     * only ever flips is_enabled, it never creates/deletes rows.
     */
    public function toggleAmenity(Request $request, DormitoryAmenity $amenity): JsonResponse
    {
        $data = $request->validate([
            'is_enabled' => ['required', 'boolean'],
        ]);

        $amenity->update(['is_enabled' => $data['is_enabled']]);

        return response()->json(['message' => 'Amenities updated.', 'amenity' => $amenity]);
    }

    /**
     * Rental Policy card: the numbers the signed Payments and Fees Schedule
     * and Tenant Agreement promise. Billing, late penalties, minimum stay
     * and the documents applicants sign all read from here, so changing a
     * value here changes both what the system does and what new tenants sign.
     * (Existing tenants keep the terms they signed: rate changes never touch
     * an existing contract's monthly rate.)
     */
    public function updatePolicy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'representative_name' => ['nullable', 'string', 'max:150'],
            'representative_position' => ['nullable', 'string', 'max:100'],
            'facebook_page_name' => ['nullable', 'string', 'max:150'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],

            'rent_due_day' => ['required', 'integer', 'min:1', 'max:28'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:31'],
            'late_penalty_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimum_stay_months' => ['required', 'integer', 'min:1', 'max:24'],
            'move_out_notice_days' => ['required', 'integer', 'min:0', 'max:180'],
            'extension_notice_days' => ['required', 'integer', 'min:0', 'max:180'],
            'deposit_refund_days' => ['required', 'integer', 'min:0', 'max:180'],
            'reservation_validity_days' => ['required', 'integer', 'min:1', 'max:180'],
            'mid_month_move_in' => ['required', Rule::in(['full', 'prorated'])],
            'water_included' => ['required', 'boolean'],
            'electricity_included' => ['required', 'boolean'],
            'wifi_included' => ['required', 'boolean'],
            'short_term_rate' => ['nullable', 'numeric', 'min:0'],
            'transient_rate' => ['nullable', 'numeric', 'min:0'],

            'rules_version' => ['nullable', 'string', 'max:20'],
            'fees_version' => ['nullable', 'string', 'max:20'],
            'documents_effective_date' => ['nullable', 'date'],
        ]);

        $profile = DormitoryProfile::current();
        $profile->fill($data)->save();

        return response()->json([
            'message' => 'Rental policy saved. New applicants will sign documents with these terms.',
        ]);
    }

    /**
     * Room Types & Rates card: add a room type. Each dorm lists its own
     * (not every dorm has the same kinds of rooms).
     */
    public function storeRoomType(Request $request): JsonResponse
    {
        $data = $this->validateRoomType($request);
        $data['sort_order'] = (int) (RoomType::max('sort_order') ?? 0) + 1;

        $type = RoomType::create($data);

        return response()->json(['message' => 'Room type added.', 'room_type' => $type->toClientArray()], 201);
    }

    /**
     * Edit a room type. Rooms of this type are re-priced right away, so new
     * applicants see the new rate. Existing contracts keep the rate their
     * tenant signed for (Payments and Fees Schedule: a change does not apply
     * to a term already paid for).
     */
    public function updateRoomType(Request $request, RoomType $roomType): JsonResponse
    {
        $roomType->update($this->validateRoomType($request));

        $roomType->rooms()->get()->each->syncFromRoomType();

        return response()->json(['message' => 'Room type updated.', 'room_type' => $roomType->fresh()->toClientArray()]);
    }

    /** Delete a room type that no room uses. */
    public function destroyRoomType(RoomType $roomType): JsonResponse
    {
        if ($roomType->rooms()->exists()) {
            return response()->json([
                'message' => 'Some rooms still use this room type. Change those rooms to another type on the Vacancy Monitoring page first.',
            ], 409);
        }

        $roomType->delete();

        return response()->json(['message' => 'Room type deleted.']);
    }

    private function validateRoomType(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'pricing_mode' => ['required', Rule::in(RoomType::PRICING_MODES)],
            'monthly_rate' => ['required', 'numeric', 'min:0'],
            'min_capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'max_capacity' => ['nullable', 'integer', 'min:1', 'max:50', 'gte:min_capacity'],
            'has_aircon' => ['nullable', 'boolean'],
        ], [
            'max_capacity.gte' => 'The maximum capacity cannot be less than the minimum.',
        ]);
    }

    /** Other Charges card: add a charge listed in the fee schedule. */
    public function storeCharge(Request $request): JsonResponse
    {
        $data = $this->validateCharge($request);
        $data['sort_order'] = (int) (DormitoryCharge::max('sort_order') ?? 0) + 1;

        $charge = DormitoryCharge::create($data);

        return response()->json(['message' => 'Charge added.', 'charge' => $charge->toClientArray()], 201);
    }

    public function updateCharge(Request $request, DormitoryCharge $charge): JsonResponse
    {
        $charge->update($this->validateCharge($request));

        return response()->json(['message' => 'Charge updated.', 'charge' => $charge->fresh()->toClientArray()]);
    }

    public function destroyCharge(DormitoryCharge $charge): JsonResponse
    {
        $charge->delete();

        return response()->json(['message' => 'Charge deleted.']);
    }

    private function validateCharge(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'amount_note' => ['nullable', 'string', 'max:100'],
            'when_applies' => ['nullable', 'string', 'max:100'],
        ]);
    }

    /**
     * House rules can now carry a section heading (e.g. "A. Conduct and
     * Respect") so the signed Rules and Regulations keep their structure.
     */
    public function storeHouseRule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rule_text' => ['required', 'string', 'max:500'],
            'section' => ['nullable', 'string', 'max:80'],
        ]);

        $nextOrder = (int) (DormitoryHouseRule::max('sort_order') ?? 0) + 1;

        $rule = DormitoryHouseRule::create([
            'section' => $data['section'] ?? null,
            'rule_text' => $data['rule_text'],
            'sort_order' => $nextOrder,
        ]);

        return response()->json(['message' => 'Rule added.', 'rule' => $rule], 201);
    }

    /**
     * House Rules list — edit the text of an existing rule.
     */
    public function updateHouseRule(Request $request, DormitoryHouseRule $houseRule): JsonResponse
    {
        $data = $request->validate([
            'rule_text' => ['required', 'string', 'max:500'],
            'section' => ['sometimes', 'nullable', 'string', 'max:80'],
        ]);

        $houseRule->update($data);

        return response()->json(['message' => 'Rule updated.', 'rule' => $houseRule]);
    }
    /**
     * House Rules list — delete a rule.
     */
    public function destroyHouseRule(DormitoryHouseRule $houseRule): JsonResponse
    {
        $houseRule->delete();

        return response()->json(['message' => 'Rule deleted.']);
    }

        private function isImageFile(?string $path): bool
    {
        if (! $path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
    }
}
