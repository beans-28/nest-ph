<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DormitoryProfile;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicController extends Controller
{
    /**
     * Landing page. Server-renders the real "Beds Available" stat; the rest
     * of the hero stats (Happy Tenants, Star Ratings) are static marketing
     * copy per the Figma design — no backing data exists for those yet
     * (reviews aren't built, Week 7 scope).
     */
    public function home()
    {
        $availableBeds = Room::withCount([
            'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
        ])->get()->sum('vacant_beds_count');

        $profile = DormitoryProfile::current();

        return view('welcome', [
            'availableBeds' => $availableBeds,
            'dormName' => $profile->dorm_name,
            'contactNumber' => $profile->contact_number,
            'contactEmail' => $profile->contact_email,
            'address' => $profile->address,
        ]);
    }

    /**
     * Public room listing for the browse page.
     *
     * Only exposes public-safe fields — no timestamps, no internal metadata.
     * A room's VR image is only included if its vr_visibility is 'public';
     * 'locked' and 'draft' rooms still appear in the listing (so prospective
     * tenants can see the room exists and its price) but without the tour.
     */
    public function rooms(Request $request): JsonResponse
    {
        $request->validate([
            'floor_id' => ['nullable', 'integer', 'exists:floors,id'],
            'status' => ['nullable', Rule::in(['available', 'full', 'maintenance'])],
            'room_type' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', Rule::in(['availability', 'price_low', 'price_high'])],
        ]);

        $query = Room::with('floor:id,floor_number,floor_name')
            ->withCount([
                'beds',
                'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
            ]);

        if ($request->filled('floor_id')) {
            $query->where('floor_id', $request->input('floor_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('room_type')) {
            $query->where('room_type', $request->input('room_type'));
        }

        match ($request->input('sort', 'availability')) {
            'price_low' => $query->orderBy('monthly_rate'),
            'price_high' => $query->orderByDesc('monthly_rate'),
            default => $query->orderByDesc('vacant_beds_count'),
        };

        return response()->json(
            $query->get()->map(fn ($room) => $this->transformPublicRoom($room))->values()
        );
    }

    /**
     * Public detail for a single room — used by the room detail / VR tour page.
     */
    public function room(Room $room): JsonResponse
    {
        $room->loadCount([
            'beds',
            'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
        ]);
        $room->load('floor:id,floor_number,floor_name');

        return response()->json($this->transformPublicRoom($room));
    }

    /**
     * Public VR tour data for one room. Returns 404 (not 403) if the room's
     * tour isn't public — a locked tour shouldn't confirm it exists.
     */
    public function roomVrTour(Room $room): JsonResponse
    {
        if ($room->vr_visibility !== 'public' || ! $room->vr_asset_path) {
            throw new NotFoundHttpException('No VR tour available for this room.');
        }

        $room->load('floor:id,floor_number');

        return response()->json([
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor?->floor_number,
            'room_type' => $room->room_type,
            'monthly_rate' => $room->monthly_rate,
            'vr_url' => Storage::disk('public')->url($room->vr_asset_path),
            'vr_caption' => $room->vr_caption,
        ]);
    }

    /**
     * Every room that has a public VR tour — for the "VR Tour" landing page
     * that lets visitors pick which room to view.
     */
    public function vrTours(): JsonResponse
    {
        $rooms = Room::with('floor:id,floor_number')
            ->where('vr_visibility', 'public')
            ->whereNotNull('vr_asset_path')
            ->orderBy('room_no')
            ->get();

        return response()->json(
            $rooms->map(fn ($room) => [
                'id' => $room->id,
                'room_no' => $room->room_no,
                'floor' => $room->floor?->floor_number,
                'room_type' => $room->room_type,
                'monthly_rate' => $room->monthly_rate,
                'vr_url' => Storage::disk('public')->url($room->vr_asset_path),
                'vr_caption' => $room->vr_caption,
            ])->values()
        );
    }

    /**
     * Dorm info + house rules for the public "Dorm Info" page, plus live
     * availability stats for the landing page hero.
     */
    public function dormInfo(): JsonResponse
    {
        $profile = DormitoryProfile::current();

        $rooms = Room::withCount([
            'beds',
            'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
        ])->get();

        return response()->json([
            'dorm_name' => $profile->dorm_name,
            'description' => $profile->description,
            'address' => $profile->address,
            'contact_number' => $profile->contact_number,
            'contact_email' => $profile->contact_email,
            'logo_url' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'payments_and_fees' => $profile->payments_and_fees,
            'house_rules' => $profile->house_rules,
            'checkout_procedures' => $profile->checkout_procedures,
            'stats' => [
                'total_rooms' => $rooms->count(),
                'total_beds' => $rooms->sum('beds_count'),
                'available_beds' => $rooms->sum('vacant_beds_count'),
                'floors' => Floor::count(),
                'starting_rate' => $rooms->where('monthly_rate', '>', 0)->min('monthly_rate'),
            ],
        ]);
    }

    /**
     * Floors + room types, for populating the browse page's filter dropdowns
     * without the frontend having to hardcode them.
     */
    public function filterOptions(): JsonResponse
    {
        return response()->json([
            'floors' => Floor::orderBy('floor_number')
                ->get(['id', 'floor_number', 'floor_name'])
                ->map(fn ($f) => [
                    'id' => $f->id,
                    'label' => $f->floor_name ?: ('Floor ' . $f->floor_number),
                ])->values(),
            'room_types' => Room::whereNotNull('room_type')
                ->distinct()
                ->orderBy('room_type')
                ->pluck('room_type')
                ->values(),
        ]);
    }

    private function transformPublicRoom(Room $room): array
    {
        $tourIsPublic = $room->vr_visibility === 'public' && $room->vr_asset_path;

        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor?->floor_number,
            'floor_label' => $room->floor?->floor_name ?: ('Floor ' . $room->floor?->floor_number),
            'room_type' => $room->room_type,
            'monthly_rate' => $room->monthly_rate,
            'status' => $room->status,
            'total_beds' => $room->beds_count,
            'available_beds' => $room->vacant_beds_count,
            'has_vr_tour' => (bool) $tourIsPublic,
            'vr_url' => $tourIsPublic ? Storage::disk('public')->url($room->vr_asset_path) : null,
            'vr_caption' => $tourIsPublic ? $room->vr_caption : null,
        ];
    }
    
        public function roomBeds(Room $room): JsonResponse
    {
        $beds = $room->beds()
            ->where('status', 'vacant')
            ->orderBy('bed_label')
            ->get(['id', 'bed_label']);
 
        return response()->json($beds);
    }

        public function dormInfoPage(): \Illuminate\View\View
    {
        $profile = DormitoryProfile::current();
 
        return view('publicdorminfo', [
            'dormName' => $profile->dorm_name,
            'description' => $profile->description,
            'address' => $profile->address,
            'contactNumber' => $profile->contact_number,
            'contactEmail' => $profile->contact_email,
            'policiesFileUrl' => $profile->policies_file_path
                ? Storage::disk('public')->url($profile->policies_file_path)
                : null,
            'paymentsAndFees' => $profile->payments_and_fees,
            'houseRules' => $profile->house_rules,
            'checkoutProcedures' => $profile->checkout_procedures,
        ]);
    }
}