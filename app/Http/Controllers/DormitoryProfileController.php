<?php

namespace App\Http\Controllers;

use App\Models\DormitoryAmenity;
use App\Models\DormitoryHouseRule;
use App\Models\DormitoryProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Use Case Report — Manage Dormitory Profile (Table 39). The Dormitory
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

        return view('admindormitoryprofile', [
            'profile' => $profile,
            'coverPhotoUrl' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'policiesFileName' => $profile->policies_file_path ? basename($profile->policies_file_path) : null,
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
     * Step 3 — upload or replace the combined legal policies & house rules
     * PDF that the public Dorm Info page displays.
     */
    public function uploadPoliciesFile(Request $request): JsonResponse
    {
        $request->validate([
            'policies_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $profile = DormitoryProfile::current();
        if (! $profile->exists) {
            $profile->save();
        }

        if ($profile->policies_file_path && Storage::disk('public')->exists($profile->policies_file_path)) {
            Storage::disk('public')->delete($profile->policies_file_path);
        }

        $path = $request->file('policies_file')->store('dormitory-profile', 'public');
        $profile->update(['policies_file_path' => $path]);

        return response()->json([
            'message' => 'Policies & house rules file uploaded.',
            'file_name' => basename($path),
        ]);
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
     * House Rules list — add a new rule. Appended to the end of the list.
     */
    public function storeHouseRule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rule_text' => ['required', 'string', 'max:500'],
        ]);

        $nextOrder = (int) (DormitoryHouseRule::max('sort_order') ?? 0) + 1;

        $rule = DormitoryHouseRule::create([
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
