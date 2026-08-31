<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VacancyController extends Controller
{
    private const BED_STATUSES = ['vacant', 'reserved', 'occupied', 'maintenance'];
    private const ROOM_STATUSES = ['available', 'full', 'maintenance'];
    private const VR_VISIBILITIES = ['public', 'locked', 'draft'];

    public function index()
    {
        $floors = Floor::with('rooms.beds')->orderBy('floor_number')->get();

        $floorGroups = $floors->map(fn ($floor) => [
            'label' => (string) $floor->floor_number,
            'rooms' => $floor->rooms->map(fn ($room) => $this->transformRoom($room, $floor))->values(),
        ])->values();

        $allBeds = Bed::all();

        $stats = [
            'total' => $allBeds->count(),
            'occupied' => $allBeds->where('status', 'occupied')->count(),
            'vacant' => $allBeds->where('status', 'vacant')->count(),
            'maintenance' => $allBeds->where('status', 'maintenance')->count(),
        ];

        return view('adminaddfloor', compact('floorGroups', 'stats'));
    }

    /**
     * Aug 2026: added amenities + photos, so rooms can carry the info the
     * public Rooms page displays (WiFi/Electricity/Water tags, listing
     * photos separate from the VR panorama). See
     * 2026_08_29_000003_add_amenities_and_room_photos migration.
     */
    public function storeRoom(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_no' => ['required', 'string', 'max:20', 'unique:rooms,room_no'],
            'floor' => ['required', 'string', 'max:10'],
            'room_type' => ['nullable', 'string', 'max:50'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:40'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'bed_count' => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses' => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(self::BED_STATUSES)],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $floor = Floor::firstOrCreate(
            ['floor_number' => (int) $data['floor']],
            ['floor_name' => 'Floor ' . $data['floor']]
        );

        $room = $floor->rooms()->create([
            'room_no' => $data['room_no'],
            'room_type' => $data['room_type'] ?? null,
            'amenities' => $data['amenities'] ?? [],
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => 'available',
        ]);

        for ($i = 0; $i < $data['bed_count']; $i++) {
            $room->beds()->create([
                'bed_label' => 'Bed ' . ($i + 1),
                'status' => $data['bed_statuses'][$i] ?? 'vacant',
            ]);
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $room->photos()->create([
                    'path' => $photo->store('room-photos', 'public'),
                    'sort_order' => $index,
                ]);
            }
        }

        $room->syncStatusFromBeds();

        return response()->json($this->transformRoom($room->fresh(['beds', 'photos']), $floor), 201);
    }

    /**
     * Same protection as destroyRoom() — checked across every room on the
     * floor, since deleting the floor would otherwise hit the identical
     * foreign key wall, just deeper into the cascade and with an uglier
     * raw SQL error to show for it.
     */
    public function destroyFloor(string $floorNumber): JsonResponse
    {
        $floor = Floor::where('floor_number', (int) $floorNumber)->firstOrFail();

        $bedIds = Bed::whereIn('room_id', $floor->rooms()->pluck('id'))->pluck('id');

        $hasHistory = \App\Models\Application::whereIn('bed_id', $bedIds)->exists()
            || \App\Models\LeaseContract::whereIn('bed_id', $bedIds)->exists();

        if ($hasHistory) {
            return response()->json([
                'message' => 'This floor can\'t be deleted — one or more of its rooms has beds with application or lease history tied to them, and removing the floor would orphan those records.',
            ], 409);
        }

        foreach ($floor->rooms as $room) {
            $room->beds()->delete();
        }
        $floor->rooms()->delete();
        $floor->delete();

        return response()->json(['message' => 'Floor deleted successfully.'], 200);
    }

    /**
     * Aug 2026: added amenities + photos (same as storeRoom). New photos are
     * appended to whatever the room already has — removing a specific photo
     * goes through deleteRoomPhoto() below, not through this endpoint.
     */
    public function updateRoom(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'room_no' => ['required', 'string', 'max:20', Rule::unique('rooms', 'room_no')->ignore($room->id)],
            'floor' => ['required', 'string', 'max:10'],
            'room_type' => ['nullable', 'string', 'max:50'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:40'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(self::ROOM_STATUSES)],
            'bed_count' => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses' => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(self::BED_STATUSES)],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $floor = Floor::firstOrCreate(
            ['floor_number' => (int) $data['floor']],
            ['floor_name' => 'Floor ' . $data['floor']]
        );

        $room->update([
            'floor_id' => $floor->id,
            'room_no' => $data['room_no'],
            'room_type' => $data['room_type'] ?? null,
            'amenities' => $data['amenities'] ?? $room->amenities,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => $data['status'] ?? $room->status,
        ]);

        $existingBeds = $room->beds()->orderBy('id')->get();

        for ($i = 0; $i < $data['bed_count']; $i++) {
            $status = $data['bed_statuses'][$i] ?? 'vacant';

            if (isset($existingBeds[$i])) {
                $existingBeds[$i]->update(['status' => $status]);
            } else {
                $room->beds()->create([
                    'bed_label' => 'Bed ' . ($i + 1),
                    'status' => $status,
                ]);
            }
        }

        if ($existingBeds->count() > $data['bed_count']) {
            $existingBeds->slice($data['bed_count'])->each->delete();
        }

        if ($request->hasFile('photos')) {
            $nextSortOrder = $room->photos()->max('sort_order') + 1;

            foreach ($request->file('photos') as $index => $photo) {
                $room->photos()->create([
                    'path' => $photo->store('room-photos', 'public'),
                    'sort_order' => $nextSortOrder + $index,
                ]);
            }
        }

        $room->syncStatusFromBeds();

        return response()->json($this->transformRoom($room->fresh(['beds', 'photos']), $floor));
    }

    /**
     * Deleting a room whose beds are tied to any application or lease
     * history would orphan those records — the database's foreign key
     * constraint already blocks this at the SQL level, but without this
     * check the admin just sees a raw SQLSTATE error instead of a clear
     * explanation of what actually went wrong.
     */
    public function destroyRoom(Room $room): JsonResponse
    {
        $bedIds = $room->beds()->pluck('id');

        $hasHistory = \App\Models\Application::whereIn('bed_id', $bedIds)->exists()
            || \App\Models\LeaseContract::whereIn('bed_id', $bedIds)->exists();

        if ($hasHistory) {
            return response()->json([
                'message' => 'This room can\'t be deleted — one or more of its beds has application or lease history tied to it, and removing the room would orphan those records. If the room is no longer usable, consider marking its beds as Maintenance instead.',
            ], 409);
        }

        $room->delete(); // beds and photos cascade via FK constraint

        return response()->json(['message' => 'Room deleted successfully.']);
    }

    public function updateBedStatus(Request $request, Bed $bed): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::BED_STATUSES)],
        ]);

        $bed->update(['status' => $data['status']]);
        $bed->room->syncStatusFromBeds();

        return response()->json($bed->fresh());
    }

    /**
     * Deletes a single room photo — both the DB record and the file on disk.
     * Kept separate from updateRoom() so the admin UI can remove one bad
     * photo without having to resend every other field.
     */
    public function deleteRoomPhoto(Room $room, RoomPhoto $photo): JsonResponse
    {
        abort_unless($photo->room_id === $room->id, 404);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully.']);
    }

    /**
     * Reorders a room's photos — for a drag-to-reorder admin UI. Optional;
     * only wire this up if the room-edit UI actually supports reordering.
     */
    public function reorderRoomPhotos(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'photo_ids' => ['required', 'array'],
            'photo_ids.*' => ['integer', 'exists:room_photos,id'],
        ]);

        foreach ($data['photo_ids'] as $index => $photoId) {
            RoomPhoto::where('id', $photoId)
                ->where('room_id', $room->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Photo order updated.']);
    }

    /**
     * Renders the VR Management page — every room with its panorama scenes
     * and the hotspots linking them, so the page can handle the whole
     * multi-scene tour in one place.
     */
    public function vrIndex()
    {
        $rooms = Room::with(['floor', 'vrScenes.hotspots.targetScene:id,title'])
            ->orderBy('room_no')
            ->get()
            ->map(fn ($room) => $this->transformVrRoom($room))
            ->values();

        return view('vrmanagement', compact('rooms'));
    }

    /**
     * Updates a room's VR caption and visibility (public / locked / draft).
     * Separate from the image upload itself, matching the VR Management
     * page's "Save" action and its quick lock/unlock toggle.
     */
    public function updateVrInfo(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'vr_caption' => ['nullable', 'string', 'max:255'],
            'vr_visibility' => ['required', Rule::in(self::VR_VISIBILITIES)],
        ]);

        $room->update([
            'vr_caption' => $data['vr_caption'] ?? null,
            'vr_visibility' => $data['vr_visibility'],
        ]);

        return response()->json($this->transformVrRoom($room->fresh(['floor', 'vrScenes.hotspots.targetScene'])));
    }

    /**
     * Aug 2026: added amenities + photos so the admin room-edit UI can
     * actually see what's currently set.
     */
    private function transformRoom(Room $room, Floor $floor): array
    {
        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => (string) $floor->floor_number,
            'room_type' => $room->room_type,
            'amenities' => $room->amenities ?? [],
            'monthly_rate' => $room->monthly_rate,
            'price_per_bed' => $room->perBedRate(),
            'status' => $room->status,
            'beds' => $room->beds->map(fn ($bed) => [
                'id' => $bed->id,
                'bed_label' => $bed->bed_label,
                'status' => $bed->status,
            ])->values(),
            'photos' => $room->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'url' => Storage::disk('public')->url($photo->path),
            ])->values(),
        ];
    }

    private function transformVrRoom(Room $room): array
    {
        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor ? (string) $room->floor->floor_number : null,
            'vr_caption' => $room->vr_caption,
            'vr_visibility' => $room->vr_visibility,
            'updated_at' => $room->updated_at?->format('M j, Y g:ia'),
            'scenes' => $room->vrScenes->map(fn ($scene) => [
                'id' => $scene->id,
                'title' => $scene->title,
                'panorama_url' => Storage::disk('public')->url($scene->panorama_path),
                'is_default' => $scene->is_default,
                'haov' => $scene->haov,
                'vaov' => $scene->vaov,
                'v_offset' => $scene->v_offset,
                'is_partial' => $scene->is_partial,
                'hotspots' => $scene->hotspots->map(fn ($hotspot) => [
                    'id' => $hotspot->id,
                    'target_scene_id' => $hotspot->target_scene_id,
                    'target_title' => $hotspot->targetScene?->title,
                    'pitch' => (float) $hotspot->pitch,
                    'yaw' => (float) $hotspot->yaw,
                    'label' => $hotspot->label,
                ])->values(),
            ])->values(),
        ];
    }
}