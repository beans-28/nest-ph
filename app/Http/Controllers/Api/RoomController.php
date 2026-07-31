<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}