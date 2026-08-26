<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class PublicRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('floor')->withCount([
            'beds',
            'beds as vacant_beds_count' => function ($q) {
                $q->where('status', 'vacant');
            },
        ]);

        if ($request->filled('floor_id')) {
            $query->where('floor_id', $request->floor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by availability: rooms with more vacant beds first
        $sort = $request->input('sort', 'availability');

        if ($sort === 'availability') {
            $query->orderByDesc('vacant_beds_count');
        } elseif ($sort === 'price_low') {
            $query->orderBy('monthly_rate');
        } elseif ($sort === 'price_high') {
            $query->orderByDesc('monthly_rate');
        }

        return $query->get([
            'id', 'floor_id', 'room_no', 'room_type', 'monthly_rate', 'status', 'vr_asset_path',
        ]);
    }
}