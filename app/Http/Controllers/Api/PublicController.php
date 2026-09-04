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
        $rooms = Room::with(['floor:id,floor_number', 'vrScenes.hotspots.targetScene:id,title'])
            ->where('vr_visibility', 'public')
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
            // A partial (phone) panorama has no image data above or below its
            // captured band, which renders as black. Constraining pitch to the
            // photo's real vertical coverage keeps the visitor inside the
            // photographed area instead of letting them tilt into the void.
            $halfVaov = max(1, (float) $scene->vaov / 2);
            $isFullSphere = (float) $scene->vaov >= 179;

            $scenes[(string) $scene->id] = [
                'title' => $scene->title,
                'panorama' => Storage::disk('public')->url($scene->panorama_path),
                // Partial (phone) panoramas need their real coverage angles,
                // otherwise Pannellum stretches them across a full sphere.
                'haov' => (float) $scene->haov,
                'vaov' => (float) $scene->vaov,
                'vOffset' => (float) $scene->v_offset,
                'minPitch' => $isFullSphere ? -90 : round((float) $scene->v_offset - $halfVaov, 2),
                'maxPitch' => $isFullSphere ? 90 : round((float) $scene->v_offset + $halfVaov, 2),
                'hotSpots' => $scene->hotspots->map(fn ($hotspot) => [
                    'pitch' => (float) $hotspot->pitch,
                    'yaw' => (float) $hotspot->yaw,
                    'type' => 'scene',
                    'text' => $hotspot->label ?: ('Go to ' . $hotspot->targetScene?->title),
                    'sceneId' => (string) $hotspot->target_scene_id,
                ])->values()->all(),
            ];
        }

        return [
            'default' => [
                'firstScene' => (string) $default->id,
                'sceneFadeDuration' => 900,
            ],
            'scenes' => $scenes,
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
     * Renders the public "Apply for Occupancy" page. Passes whether a
     * contract template has been uploaded, so the page can show a graceful
     * message instead of a broken link if the admin hasn't added one yet.
     */
    public function applyPage(): \Illuminate\View\View
    {
        $profile = DormitoryProfile::current();

        return view('publicapply', [
            'hasContractTemplate' => (bool) $profile->contract_template_path,
        ]);
    }

    /**
     * Use Case Report — Apply for Occupancy, step 4.1: "Display the full
     * dormitory contract for review." Streamed the same way as the Dorm Info
     * policies PDF — reading directly through Storage rather than the
     * public/storage symlink, which sidesteps a known bug where PHP's
     * built-in dev server (php artisan serve) returns 403 for symlinked
     * paths on Windows.
     */
    public function contractTemplateView()
    {
        $profile = DormitoryProfile::current();

        abort_unless($profile->contract_template_path, 404);

        return Storage::disk('public')->response($profile->contract_template_path);
    }

    /**
     * Step 5.1: "Generate and serve the downloadable contract file."
     */
    public function contractTemplateDownload()
    {
        $profile = DormitoryProfile::current();

        abort_unless($profile->contract_template_path, 404);

        return Storage::disk('public')->download(
            $profile->contract_template_path,
            'NEST-PH-Dormitory-Contract.pdf'
        );
    }
    /**
     * Renders the public "Dorm Info" page.
     */
    public function dormInfoPage(): \Illuminate\View\View
    {
        $profile = DormitoryProfile::current();

        return view('publicdorminfo', [
            'dormName' => $profile->dorm_name,
            'description' => $profile->description,
            'address' => $profile->address,
            'contactNumber' => $profile->contact_number,
            'contactEmail' => $profile->contact_email,
            // The view uses this as both a truthy check ("has a file been
            // uploaded?") and the actual iframe/download href -- it was
            // previously only being passed a boolean (hasPoliciesFile),
            // which the view never actually referenced, so $policiesFileUrl
            // was always undefined.
            'policiesFileUrl' => $profile->policies_file_path
                ? route('public.dorminfo.file')
                : null,
            'paymentsAndFees' => $profile->payments_and_fees,
            'houseRules' => $profile->house_rules,
            'checkoutProcedures' => $profile->checkout_procedures,
        ]);
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
}