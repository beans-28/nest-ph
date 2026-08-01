<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class VacancyController extends Controller
{
    /**
     * Show the Vacancy Monitoring page.
     * Floors aren't a table in this schema — they're just the distinct
     * `rooms.floor` values, so we group rooms by that column here.
     */
    public function index()
    {
        $rooms = Room::with('beds')->orderBy('floor')->orderBy('room_no')->get();

        $grouped = $rooms->groupBy(fn ($room) => $room->floor ?: 'Unassigned');

        $allBeds = $rooms->flatMap(fn ($room) => $room->beds);

        $stats = [
            'total'       => $allBeds->count(),
            'occupied'    => $allBeds->where('status', 'occupied')->count(),
            'vacant'      => $allBeds->where('status', 'vacant')->count(),
            'maintenance' => $allBeds->where('status', 'maintenance')->count(),
        ];

        $floorGroups = $grouped->map(fn ($rooms, $floorLabel) => [
            'label' => (string) $floorLabel,
            'rooms' => $rooms->values()->map(fn ($room) => [
                'id'           => $room->id,
                'room_no'      => $room->room_no,
                'floor'        => (string) $room->floor,
                'room_type'    => $room->room_type,
                'monthly_rate' => $room->monthly_rate,
                'status'       => $room->status,
                'beds'         => $room->beds->map(fn ($bed) => [
                    'id'        => $bed->id,
                    'bed_label' => $bed->bed_label,
                    'status'    => $bed->status,
                ])->values(),
            ])->values(),
        ])->values();

        return view('adminaddfloor', compact('grouped', 'stats', 'floorGroups'));
    }

    /* -------------------------------------------------------------- */
    /*  ROOMS                                                          */
    /* -------------------------------------------------------------- */

    public function storeRoom(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_no'        => ['required', 'string', 'max:20', 'unique:rooms,room_no'],
            'floor'          => ['required', 'string', 'max:10'],
            'room_type'      => ['nullable', 'string', 'max:50'],
            'monthly_rate'   => ['nullable', 'numeric', 'min:0'],
            'bed_count'      => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses'   => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(Bed::STATUSES)],
        ]);

        $room = Room::create([
            'room_no'      => $data['room_no'],
            'floor'        => $data['floor'],
            'room_type'    => $data['room_type'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status'       => 'available',
        ]);

        for ($i = 0; $i < $data['bed_count']; $i++) {
            $room->beds()->create([
                'bed_label' => 'Bed ' . ($i + 1),
                'status'    => $data['bed_statuses'][$i] ?? 'vacant',
            ]);
        }

        $room->syncStatusFromBeds();

        return response()->json($room->fresh('beds'), 201);
    }

    public function updateRoom(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'room_no'        => ['required', 'string', 'max:20', Rule::unique('rooms', 'room_no')->ignore($room->id)],
            'floor'          => ['required', 'string', 'max:10'],
            'room_type'      => ['nullable', 'string', 'max:50'],
            'monthly_rate'   => ['nullable', 'numeric', 'min:0'],
            'status'         => ['nullable', Rule::in(Room::STATUSES)],
            'bed_count'      => ['required', 'integer', 'min:1', 'max:8'],
            'bed_statuses'   => ['nullable', 'array'],
            'bed_statuses.*' => [Rule::in(Bed::STATUSES)],
        ]);

        $room->update([
            'room_no'      => $data['room_no'],
            'floor'        => $data['floor'],
            'room_type'    => $data['room_type'] ?? null,
            'monthly_rate' => $data['monthly_rate'] ?? 0,
            'status'       => $data['status'] ?? $room->status,
        ]);

        $existingBeds = $room->beds()->orderBy('id')->get();

        for ($i = 0; $i < $data['bed_count']; $i++) {
            $status = $data['bed_statuses'][$i] ?? 'vacant';

            if (isset($existingBeds[$i])) {
                $existingBeds[$i]->update(['status' => $status]);
            } else {
                $room->beds()->create([
                    'bed_label' => 'Bed ' . ($i + 1),
                    'status'    => $status,
                ]);
            }
        }

        // Remove surplus beds if the room shrank
        if ($existingBeds->count() > $data['bed_count']) {
            $existingBeds->slice($data['bed_count'])->each->delete();
        }

        $room->syncStatusFromBeds();

        return response()->json($room->fresh('beds'));
    }

    public function destroyRoom(Room $room): JsonResponse
    {
        $room->delete(); // beds cascade via FK constraint

        return response()->json(['deleted' => true]);
    }

    /* -------------------------------------------------------------- */
    /*  BEDS                                                           */
    /* -------------------------------------------------------------- */

    public function updateBedStatus(Request $request, Bed $bed): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Bed::STATUSES)],
        ]);

        $bed->update(['status' => $data['status']]);
        $bed->room->syncStatusFromBeds();

        return response()->json($bed->fresh());
    }
}
