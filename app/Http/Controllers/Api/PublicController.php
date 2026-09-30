<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DormitoryProfile;
use App\Models\Floor;
use App\Models\Room;
use App\Models\VrScene;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicController extends Controller
{
    /**
     * Virtual tour zoom limits, in degrees of view width. 125° starts wide
     * enough to take in most of a room; each scene may lower the zoom-out
     * limit further so the view never goes past the edge of its photo.
     */
    private const VIEWER_START_HFOV = 125;
    private const VIEWER_MIN_HFOV = 50;
    private const VIEWER_MAX_HFOV = 140;

   /**
    * Landing page. Server-renders "Beds Available" and "Happy Tenants" from
    * real data; star ratings come from the Review model. Happy Tenants uses
    * the same active-tenant definition as DashboardController and
    * TenantController to keep all three counts consistent.
    */
    public function home()
    {
        $availableBeds = Room::withCount([
            'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
        ])->get()->sum('vacant_beds_count');

        $profile = DormitoryProfile::current();
        $reviewStats = \App\Models\Review::aggregate();

        return view('welcome', [
            'availableBeds' => $availableBeds,
            'dormName' => $profile->dorm_name,
            'description' => $profile->description,
            'contactNumber' => $profile->contact_number,
            'contactEmail' => $profile->contact_email,
            'address' => $profile->address,
            'coverPhotoUrl' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'isBirVerified' => $profile->isBirVerified(),
            'birRegistrationImageUrl' => $this->isImageFile($profile->bir_registration_path)
                ? Storage::disk('public')->url($profile->bir_registration_path)
                : null,
            'happyTenantsCount' => \App\Models\Tenant::where('status', 'active')
                ->where('is_blacklisted', false)
                ->count(),
            'availableResources' => \App\Models\DormitoryAmenity::where('is_enabled', true)
                ->orderBy('sort_order')
                ->pluck('label')
                ->take(2)
                ->implode(', '),
            'averageRating' => $reviewStats['average'],
            'reviewCount' => $reviewStats['count'],
        ]);
    }

    /**
     * Public room listing for the browse page.
     *
     * Only exposes public-safe fields — no timestamps, no internal metadata.
     * A room's VR image is only included if its vr_visibility is 'public';
     * 'locked' and 'draft' rooms still appear in the listing (so prospective
     * tenants can see the room exists and its price) but without the tour.
     *
     * Aug 2026: now also eager-loads beds and photos so transformPublicRoom()
     * can expose per-bed status (for the Rooms page's room/bed status cards)
     * and listing photos (separate from the VR panorama) without N+1 queries.
     *
     * Default ordering (this update): floor, then room number, ascending.
     * The previous default sorted by vacant-bed count descending, which
     * reads as a random/arbitrary order to a visitor since the page has no
     * sort control exposing that logic — floor-then-room-number is what
     * people actually expect when browsing a room list.
     */
    public function rooms(Request $request): JsonResponse
    {
        $request->validate([
            'floor_id' => ['nullable', 'integer', 'exists:floors,id'],
            'status' => ['nullable', Rule::in(['available', 'full', 'maintenance'])],
            'room_type' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', Rule::in(['availability', 'price_low', 'price_high'])],
        ]);

        $query = Room::with([
            'floor:id,floor_number,floor_name',
            'beds:id,room_id,bed_label,status',
            'photos:id,room_id,path,sort_order',
            'vrScenes:id,room_id,panorama_path,is_default',
        ])->withCount([
            'beds',
            'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
            'vrScenes',
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

        $sort = $request->input('sort', 'availability');

        match ($sort) {
            'price_low' => $query->orderBy('monthly_rate'),
            'price_high' => $query->orderByDesc('monthly_rate'),
            // Default (floor, then room number) is sorted in PHP below,
            // once relations/counts are already loaded -- doing it via a
            // DB-level join here previously required a raw select('rooms.*')
            // that silently wiped out withCount()'s own added select
            // columns (select() replaces the select list rather than
            // appending to it), which broke has_vr_tour for every room.
            default => null,
        };

        $rooms = $query->get();

        if (! in_array($sort, ['price_low', 'price_high'], true)) {
            $rooms = $rooms->sortBy([
                [fn ($room) => $room->floor?->floor_number ?? 0, 'asc'],
                [fn ($room) => (int) preg_replace('/\D/', '', (string) $room->room_no), 'asc'],
            ])->values();
        }

        return response()->json(
            $rooms->map(fn ($room) => $this->transformPublicRoom($room))->values()
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
            'vrScenes',
        ]);
        $room->load([
            'floor:id,floor_number,floor_name',
            'beds:id,room_id,bed_label,status',
            'photos:id,room_id,path,sort_order',
            'vrScenes:id,room_id,panorama_path,is_default',
        ]);

        return response()->json($this->transformPublicRoom($room));
    }

    /**
     * Public VR tour data for one room, in the shape Pannellum's tour config
     * expects: a set of named scenes, each with its panorama and the hotspots
     * leading to other scenes. Returns 404 (not 403) if the tour isn't public
     * — a locked tour shouldn't confirm it exists.
     */
    public function roomVrTour(Room $room): JsonResponse
    {
        $room->load(['floor:id,floor_number', 'vrScenes.hotspots.targetScene:id,title']);

        if ($room->vr_visibility !== 'public' || $room->vrScenes->isEmpty()) {
            throw new NotFoundHttpException('No VR tour available for this room.');
        }

        return response()->json([
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor?->floor_number,
            'room_type' => $room->room_type,
            'monthly_rate' => $room->monthly_rate,
            'vr_caption' => $room->vr_caption,
            'tour' => $this->buildTourConfig($room),
        ]);
    }

    /**
     * Every room with a public VR tour — for the "VR Tour" page's room picker.
     */
    public function vrTours(): JsonResponse
    {
        // Only rooms someone can actually move into: the tour page labels
        // this list "Available rooms", so full or under-maintenance rooms
        // are left out.
        $rooms = Room::with(['floor:id,floor_number', 'vrScenes.hotspots.targetScene:id,title'])
            ->withCount([
                'beds', // total beds = how many people the room fits
                'beds as vacant_beds_count' => fn ($q) => $q->where('status', 'vacant'),
            ])
            ->where('vr_visibility', 'public')
            ->where('status', 'available')
            ->whereHas('vrScenes')
            ->orderBy('room_no')
            ->get();

        return response()->json(
            $rooms->map(fn ($room) => [
                'id' => $room->id,
                'room_no' => $room->room_no,
                'floor' => $room->floor?->floor_number,
                'room_type' => $room->room_type,
                'monthly_rate' => $room->monthly_rate,
                'vr_caption' => $room->vr_caption,
                'vacant_beds' => $room->vacant_beds_count,
                'capacity' => $room->beds_count,
                'scene_count' => $room->vrScenes->count(),
                'thumbnail_url' => $room->vrScenes->isNotEmpty()
                    ? Storage::disk('public')->url(
                        ($room->vrScenes->firstWhere('is_default') ?? $room->vrScenes->first())->panorama_path
                    )
                    : null,
                'tour' => $this->buildTourConfig($room),
            ])->values()
        );
    }

    /**
     * Shapes a room's scenes into Pannellum's { default, scenes } tour config.
     * Scene ids are stringified because Pannellum uses them as object keys.
     */
    private function buildTourConfig(Room $room): array
    {
        $default = $room->vrScenes->firstWhere('is_default') ?? $room->vrScenes->first();

        $scenes = [];

        foreach ($room->vrScenes as $scene) {
            $scenes[(string) $scene->id] = array_merge(
                [
                    'title' => $scene->title,
                    // Shown as "Last updated on ..." over the photo.
                    'updatedAt' => $scene->photo_updated_at?->toIso8601String(),
                ],
                $this->sceneView($scene),
                [
                    'hotSpots' => $scene->hotspots->map(fn ($hotspot) => [
                        'pitch' => (float) $hotspot->pitch,
                        'yaw' => (float) $hotspot->yaw,
                        'type' => 'scene',
                        'text' => $hotspot->label ?: ('Go to ' . $hotspot->targetScene?->title),
                        'sceneId' => (string) $hotspot->target_scene_id,
                    ])->values()->all(),
                ],
            );
        }

        return [
            'default' => [
                'firstScene' => (string) $default->id,
                'sceneFadeDuration' => 900,
                // Slowly turn the room on its own so it feels alive before the
                // visitor touches it; it stops as soon as they drag, and
                // resumes after a few idle seconds.
                'autoRotate' => -2,
                'autoRotateInactivityDelay' => 5000,
            ],
            'scenes' => $scenes,
        ];
    }

    /**
     * Which image to show and how far the visitor may look and zoom.
     *
     * - True 360 photo (e.g. from the 360 Photo Cam app): free look in every
     *   direction.
     * - Phone panorama with a painted ceiling/floor (filled_path): the filled
     *   image covers the full 180° up-down, so the visitor can look straight
     *   up and down.
     * - Phone panorama without one (painting failed): keep the camera inside
     *   the photographed band, as before.
     *
     * Zoom-out is capped so the view is never wider or taller than the photo,
     * which is what caused black bars even when the visitor didn't tilt.
     */
    private function sceneView(VrScene $scene): array
    {
        $haov = (float) $scene->haov;
        $vaov = (float) $scene->vaov;
        $vOffset = (float) $scene->v_offset;
        $isFullSphere = $vaov >= 179;
        // filled_path is the painted copy for phone panoramas, or a shrunk
        // phone-safe copy for large full 360 photos.
        $filled = (bool) $scene->filled_path;

        if ($isFullSphere || $filled) {
            $view = [
                'panorama' => Storage::disk('public')->url($filled ? $scene->filled_path : $scene->panorama_path),
                'haov' => $haov,
                'vaov' => 180.0,
                'vOffset' => 0.0,
                'minPitch' => -90,
                'maxPitch' => 90,
            ];
            $maxHfov = min(self::VIEWER_MAX_HFOV, $haov);
        } else {
            $halfVaov = max(1, $vaov / 2);
            $view = [
                'panorama' => Storage::disk('public')->url($scene->panorama_path),
                'haov' => $haov,
                'vaov' => $vaov,
                'vOffset' => $vOffset,
                'minPitch' => round($vOffset - $halfVaov, 2),
                'maxPitch' => round($vOffset + $halfVaov, 2),
            ];
            // On a landscape screen the view is shorter than it is wide, so
            // capping the width at the photo's height keeps black out.
            $maxHfov = min(self::VIEWER_MAX_HFOV, $haov, $vaov);
        }

        $maxHfov = max(self::VIEWER_MIN_HFOV + 1, round($maxHfov, 2));

        return $view + [
            'hfov' => min(self::VIEWER_START_HFOV, $maxHfov),
            'minHfov' => self::VIEWER_MIN_HFOV,
            'maxHfov' => $maxHfov,
        ];
    }

    /**
     * Dorm info + house rules for the public "Dorm Info" page, plus live
     * availability stats for the landing page hero.
     *
     * NOTE: this is the JSON API version (kept for any API consumer that
     * wants it). The actual /dorm-info page uses dormInfoPage() below, which
     * server-renders the Blade view directly.
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
            // Same fields the real /dorm-info page shows (dormInfoPage() below).
            'cover_photo_url' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'brand_logo_url' => $profile->brandLogoUrl(),
            'is_bir_verified' => $profile->isBirVerified(),
            'amenities' => \App\Models\DormitoryAmenity::where('is_enabled', true)->orderBy('sort_order')->pluck('label'),
            'house_rules_list' => \App\Models\DormitoryHouseRule::orderBy('sort_order')->orderBy('id')->pluck('rule_text'),
            'policies_file_url' => $profile->policies_file_path ? route('public.dorminfo.file') : null,
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

    /**
     * Aug 2026: added per-bed status (beds), amenities, and listing photo
     * URLs (photo_url / photo_urls) — needed for the Rooms page's room/bed
     * status cards and photo listing cards. See
     * 2026_08_29_000003_add_amenities_and_room_photos migration.
     *
     * photo_url fallback: if a room has no separate listing photo but does
     * have a published VR tour, use that tour's default panorama as the
     * thumbnail instead of leaving it blank.
     *
     * floor_label (this update): always "Floor {floor_number}", not
     * floor_name. The admin Vacancy Monitoring page already displays floors
     * this way (floor_name isn't editable anywhere in the current UI), so
     * a stale/incorrect floor_name value was showing the wrong label here
     * while the admin page showed the correct one. Flag to PELEA/BAGUI if
     * custom floor names (e.g. "Ground Floor") should become a real,
     * editable feature later.
     */
    private function transformPublicRoom(Room $room): array
    {
        // A tour now exists when the room has at least one panorama scene,
        // not when the retired single vr_asset_path is set.
        $tourIsPublic = $room->vr_visibility === 'public' && $room->vr_scenes_count > 0;

        $photoUrl = $room->photos->first()
            ? Storage::disk('public')->url($room->photos->first()->path)
            : null;

        if (! $photoUrl && $tourIsPublic && $room->vrScenes->isNotEmpty()) {
            $defaultScene = $room->vrScenes->firstWhere('is_default') ?? $room->vrScenes->first();
            $photoUrl = Storage::disk('public')->url($defaultScene->panorama_path);
        }

        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor?->floor_number,
            'floor_label' => 'Floor ' . $room->floor?->floor_number,
            'room_type' => $room->room_type,
            'amenities' => $room->amenities ?? [],
            'monthly_rate' => $room->monthly_rate,
            'price_per_bed' => $room->perBedRate(),
            'status' => $room->status,
            'total_beds' => $room->beds_count,
            'available_beds' => $room->vacant_beds_count,
            'beds' => $room->beds->map(fn ($bed) => [
                'label' => $bed->bed_label,
                'status' => $bed->status,
            ])->values(),
            'photo_url' => $photoUrl,
            'photo_urls' => $room->photos->map(
                fn ($photo) => Storage::disk('public')->url($photo->path)
            )->values(),
            'has_vr_tour' => (bool) $tourIsPublic,
            'vr_caption' => $tourIsPublic ? $room->vr_caption : null,
        ];
    }

    /**
     * Vacant beds for a single room — used by the "Apply for Occupancy"
     * form's Bed No dropdown once a Room No has been chosen.
     */
    public function roomBeds(Room $room): JsonResponse
    {
        $beds = $room->beds()
            ->where('status', 'vacant')
            ->orderBy('bed_label')
            ->get(['id', 'bed_label']);

        return response()->json($beds);
    }

    /**
     * Renders the public "Apply for Occupancy" page. Its contract is
     * generated live (contract-preview / contract-sign); the old
     * uploaded-template routes were removed in v38.
     */
    public function applyPage(): \Illuminate\View\View
    {
        return view('publicapply');
    }

    /**
     * Streams the policies PDF for inline viewing (used as the iframe src on
     * the Dorm Info page). Reads the file directly through Storage instead of
     * the public/storage symlink — sidesteps a known bug where PHP's built-in
     * dev server (php artisan serve) returns 403 for symlinked paths on
     * Windows, even when the file exists and is readable.
     */
    public function policiesFileView()
    {
        $profile = DormitoryProfile::current();

        abort_unless($profile->policies_file_path, 404);

        return Storage::disk('public')->response($profile->policies_file_path);
    }

    /**
     * Same file, but forces a real download (Content-Disposition: attachment)
     * with a friendly filename, for the "Download PDF" button.
     */
    public function policiesFileDownload()
    {
        $profile = DormitoryProfile::current();

        abort_unless($profile->policies_file_path, 404);

        return Storage::disk('public')->download(
            $profile->policies_file_path,
            'Dormitory-Policies-and-Rules.pdf'
        );
    }

    private function isImageFile(?string $path): bool
    {
        if (! $path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
    }
}