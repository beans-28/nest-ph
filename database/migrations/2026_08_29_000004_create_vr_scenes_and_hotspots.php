<?php

use App\Models\Room;
use App\Models\VrScene;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A room's VR tour is made of several panorama "scenes" — e.g. entrance,
        // bedside, study corner — that the visitor moves between, Street View
        // style. Replaces the single rooms.vr_asset_path panorama.
        Schema::create('vr_scenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('title', 100);
            $table->string('panorama_path', 255);
            $table->boolean('is_default')->default(false); // the scene the tour opens on
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // A clickable arrow inside a scene that moves the visitor to another
        // scene. pitch/yaw are the 3D angles (in degrees) where the arrow sits
        // on the panorama sphere — captured by clicking in the admin editor,
        // never typed by hand.
        Schema::create('vr_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vr_scene_id')->constrained('vr_scenes')->cascadeOnDelete();
            $table->foreignId('target_scene_id')->constrained('vr_scenes')->cascadeOnDelete();
            $table->decimal('pitch', 8, 4);
            $table->decimal('yaw', 8, 4);
            $table->string('label', 100)->nullable();
            $table->timestamps();
        });

        // Carry over any panorama already uploaded under the old single-image
        // system so existing VR tours don't silently disappear.
        Room::whereNotNull('vr_asset_path')->get()->each(function ($room) {
            VrScene::create([
                'room_id' => $room->id,
                'title' => $room->vr_caption ?: 'Main View',
                'panorama_path' => $room->vr_asset_path,
                'is_default' => true,
                'sort_order' => 0,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vr_hotspots');
        Schema::dropIfExists('vr_scenes');
    }
};
