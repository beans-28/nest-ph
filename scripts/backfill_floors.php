<?php
// scripts/backfill_floors.php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Determine floor columns and create a default floor compatible with your schema
$cols = Schema::getColumnListing('floors');

if (Floor::count() === 0) {
    if (in_array('floor_name', $cols, true) && in_array('floor_number', $cols, true)) {
        $floor = Floor::create([
            'floor_name' => '1st Floor',
            'floor_number' => 1,
        ]);
        echo "Created default floor (legacy cols): id={$floor->id}\n";
    } elseif (in_array('name', $cols, true) && in_array('sort_order', $cols, true)) {
        $id = DB::table('floors')->insertGetId([
            'name' => '1st Floor',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $floor = Floor::find($id);
        echo "Created default floor (name/sort_order): id={$floor->id}\n";
    } else {
        // fallback: create a minimal record via DB with whatever columns exist
        $data = ['created_at' => now(), 'updated_at' => now()];
        if (in_array('name', $cols, true)) $data['name'] = '1st Floor';
        if (in_array('floor_name', $cols, true)) $data['floor_name'] = '1st Floor';
        if (in_array('sort_order', $cols, true)) $data['sort_order'] = 1;
        if (in_array('floor_number', $cols, true)) $data['floor_number'] = 1;
        $id = DB::table('floors')->insertGetId($data);
        $floor = Floor::find($id);
        echo "Created default floor (fallback): id={$floor->id}\n";
    }
} else {
    $floor = Floor::first();
    echo "Using existing floor: id={$floor->id}\n";
}

// Assign rooms without floor_id to the default floor
$updated = Room::whereNull('floor_id')->orWhere('floor_id', 0)->update(['floor_id' => $floor->id]);

echo "Assigned {$updated} room(s) to floor_id={$floor->id}\n";

// Recompute room statuses where needed
$rooms = Room::where('floor_id', $floor->id)->get();
foreach ($rooms as $r) {
    try {
        $r->syncStatusFromBeds();
    } catch (Throwable $e) {
        // ignore
    }
}

echo "Backfill complete.\n";
