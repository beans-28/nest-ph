<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // e.g. ["wifi", "electricity", "water"] — free-form so admin isn't
            // locked into a fixed list, matches the checklist look in the
            // Figma room cards ("WIFI", "ELECTRICITY", "WATER").
            $table->json('amenities')->nullable()->after('room_type');
        });

        // Separate from vr_asset_path (which is one 360° panorama for the VR
        // tour). This is for the regular listing photos shown on the public
        // Rooms page cards — a room can have several (bedroom, kitchen, etc.).
        Schema::create('room_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('path', 255);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_photos');

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('amenities');
        });
    }
};
