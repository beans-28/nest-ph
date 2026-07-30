<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        return Floor::withCount('rooms')->orderBy('floor_number')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_name' => 'required|string|max:50',
            'floor_number' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $floor = Floor::create($validated);

        return response()->json($floor, 201);
    }

    public function show(Floor $floor)
    {
        return $floor->load('rooms');
    }

    public function update(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'floor_name' => 'sometimes|required|string|max:50',
            'floor_number' => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $floor->update($validated);

        return response()->json($floor);
    }

    public function destroy(Floor $floor)
    {
        if ($floor->rooms()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a floor that still has rooms assigned to it.',
            ], 409);
        }

        $floor->delete();

        return response()->json(['message' => 'Floor deleted successfully.']);
    }
}