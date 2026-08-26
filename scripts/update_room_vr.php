<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$r = \App\Models\Room::first();
if ($r) {
    $r->vr_asset_path = 'vr-assets/test_vr.png';
    $r->vr_caption = 'Sample caption';
    $r->vr_visibility = 'public';
    $r->save();
    echo "updated room {$r->id}\n";
} else {
    echo "no room\n";
}
