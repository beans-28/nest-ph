<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Floor $floor)
    {
        return $floor->rooms()->withCount('beds')->get();
    }

    public function store(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'room_no' => 'required|string|max:20',
            'room_type' => 'nullable|string|max:50',
            'monthly_rate' => 'required|numeric|min:0',
            'status' => 'nullable|in:available,full,maintenance',
        ]);

        $room = $floor->rooms()->create($validated);

        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        return $room->load('beds', 'floor');
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_no' => 'sometimes|required|string|max:20',
            'room_type' => 'nullable|string|max:50',
            'monthly_rate' => 'sometimes|required|numeric|min:0',
            'status' => 'nullable|in:available,full,maintenance',
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy(Room $room)
    {
        if ($room->beds()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a room that still has beds assigned to it.',
            ], 409);
        }

        $room->delete();

        return response()->json(['message' => 'Room deleted successfully.']);
    }
}