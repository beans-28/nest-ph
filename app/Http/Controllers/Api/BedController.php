<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BedController extends Controller
{
    public function index(Room $room)
    {
        return $room->beds;
    }

    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'bed_label' => [
                'required',
                'string',
                'max:20',
                Rule::unique('beds', 'bed_label')->where(fn ($query) => $query->where('room_id', $room->id)),
            ],
            'status' => 'nullable|in:vacant,occupied,maintenance',
        ]);

        $bed = $room->beds()->create($validated);

        return response()->json($bed, 201);
    }

    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'bed_label' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('beds', 'bed_label')
                    ->where(fn ($query) => $query->where('room_id', $bed->room_id))
                    ->ignore($bed->id),
            ],
            'status' => 'nullable|in:vacant,occupied,maintenance',
        ]);

        $bed->update($validated);

        return response()->json($bed);
    }

    public function destroy(Bed $bed)
    {
        $bed->delete();

        return response()->json(['message' => 'Bed deleted successfully.']);
    }
}