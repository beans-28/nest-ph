<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VacancyController extends Controller
{
    private const BED_STATUSES = ['vacant', 'occupied', 'maintenance'];
    private const ROOM_STATUSES = ['available', 'full', 'maintenance'];

    public function index()
    {
        $floorsQuery = Floor::with('rooms.beds');

        if (Schema::hasColumn('floors', 'floor_number')) {
            $floorsQuery->orderBy('floor_number');
        } else {
            $floorsQuery->orderBy('id');
        }

        $floors = $floorsQuery->get();

        $floorGroups = $floors->map(fn ($floor) => [
            'label' => (string) ($floor->floor_number ?? $floor->id),
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

    public function storeRoom(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_no' => ['required', 'string', 'max:20', 'unique:rooms,room_no'],
            'floor' => ['required', 'string', 'max:10'],
            'room_type' => ['nullable', 'string', 'max:50'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'bed_count' => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses' => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(self::BED_STATUSES)],
        ]);

        $floor = Floor::firstOrCreate(
            ['floor_number' => (int) $data['floor']],
            ['floor_name' => 'Floor ' . $data['floor']]
        );

        $room = $floor->rooms()->create([
            'room_no' => $data['room_no'],
            'room_type' => $data['room_type'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => 'available',
        ]);

        for ($i = 0; $i < $data['bed_count']; $i++) {
            $room->beds()->create([
                'bed_label' => 'Bed ' . ($i + 1),
                'status' => $data['bed_statuses'][$i] ?? 'vacant',
            ]);
        }

        $room->syncStatusFromBeds();

        return response()->json($this->transformRoom($room->fresh('beds'), $floor), 201);
    }

    public function destroyFloor(string $floorNumber): JsonResponse
    {
        $floor = Floor::where('floor_number', (int) $floorNumber)->firstOrFail();

        foreach ($floor->rooms as $room) {
            $room->beds()->delete();
        }
        $floor->rooms()->delete();
        $floor->delete();

        return response()->json(['message' => 'Floor deleted successfully.'], 200);
    }

    public function updateRoom(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'room_no' => ['required', 'string', 'max:20', Rule::unique('rooms', 'room_no')->ignore($room->id)],
            'floor' => ['required', 'string', 'max:10'],
            'room_type' => ['nullable', 'string', 'max:50'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(self::ROOM_STATUSES)],
            'bed_count' => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses' => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(self::BED_STATUSES)],
        ]);

        $floor = Floor::firstOrCreate(
            ['floor_number' => (int) $data['floor']],
            ['floor_name' => 'Floor ' . $data['floor']]
        );

        $room->update([
            'floor_id' => $floor->id,
            'room_no' => $data['room_no'],
            'room_type' => $data['room_type'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status' => $data['status'] ?? $room->status,
        ]);

        // Position-based sync: update existing beds in place, add/remove only the difference
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

        $room->syncStatusFromBeds();

        return response()->json($this->transformRoom($room->fresh('beds'), $floor));
    }

    public function destroyRoom(Room $room): JsonResponse
    {
        $room->delete(); // beds cascade via FK constraint

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

    public function updateVrInfo(Request $request, Room $room)
    {
        $data = $request->validate([
            'vr_caption' => ['nullable', 'string', 'max:255'],
            'vr_visibility' => ['nullable', 'string', 'in:public,locked,draft'],
        ]);

        $updates = [];
        if (Schema::hasColumn('rooms', 'vr_caption') && array_key_exists('vr_caption', $data)) {
            $updates['vr_caption'] = $data['vr_caption'];
        }
        if (Schema::hasColumn('rooms', 'vr_visibility') && array_key_exists('vr_visibility', $data)) {
            $updates['vr_visibility'] = $data['vr_visibility'];
        }

        if (!empty($updates)) {
            $room->update($updates);
        }

        return response()->json($this->transformRoom($room->fresh(), $room->floor));
    }

    public function deleteVrImage(Room $room)
    {
        if ($room->vr_asset_path) {
            Storage::disk('public')->delete($room->vr_asset_path);
            $room->update(['vr_asset_path' => null]);
        }

        return response()->json(['room' => $this->transformRoom($room->fresh(), $room->floor)]);
    }

    public function uploadVrImage(Request $request, Room $room)
    {
        $request->validate([
            'vr_image' => 'required|file|mimes:jpg,jpeg,png|max:10240', // max 10MB
        ]);

        // Delete the old file first, if one exists
        if ($room->vr_asset_path) {
            Storage::disk('public')->delete($room->vr_asset_path);
        }

        $path = $request->file('vr_image')->store('vr-assets', 'public');

        $room->update(['vr_asset_path' => $path]);

        return response()->json([
            'message' => 'VR image uploaded successfully.',
            'vr_asset_path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function transformRoom(Room $room, Floor $floor = null): array
    {
        // Ensure we have a Floor model instance. Accessing `$room->floor` can
        // return the legacy string column if the old `floor` attribute still
        // exists on the model. Use the relation accessor to always resolve
        // the related Floor model when needed.
        $floor = $floor ?? $room->floor()->first();

        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $floor ? (string) $floor->floor_number : null,
            'room_type' => $room->room_type,
            'monthly_rate' => $room->monthly_rate,
            'status' => $room->status,
            'vr_asset_path' => $room->vr_asset_path ?? null,
            'vr_url' => $room->vr_asset_path ? Storage::disk('public')->url($room->vr_asset_path) : null,
            'vr_caption' => $room->vr_caption ?? null,
            'vr_visibility' => $room->vr_visibility ?? 'draft',
            'updated_at' => $room->updated_at ? $room->updated_at->toDateTimeString() : null,
            'beds' => $room->beds->map(fn ($bed) => [
                'id' => $bed->id,
                'bed_label' => $bed->bed_label,
                'status' => $bed->status,
            ])->values(),
        ];
    }

    // Admin-facing VR index
    public function vrIndex()
    {
        // Avoid passing the raw `floor` attribute (which may be a string if the
        // legacy `floor` column exists). Let transformRoom() load the related
        // Floor model from the relation to ensure a proper `Floor` instance.
        $rooms = Room::with('floor', 'beds')->get()->map(fn($r) => $this->transformRoom($r))->values();
        return view('vrmanagement', compact('rooms'));
    }
}