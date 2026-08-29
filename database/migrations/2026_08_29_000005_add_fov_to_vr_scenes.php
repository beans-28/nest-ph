<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            // How much of the world the photo actually covers. A true 360 camera
            // gives 360x180; a phone's Panorama mode gives a partial sweep
            // (e.g. 210x55). Storing these lets Pannellum place a normal phone
            // panorama on the sphere correctly instead of stretching it.
            $table->decimal('haov', 6, 2)->default(360); // horizontal angle of view
            $table->decimal('vaov', 6, 2)->default(180); // vertical angle of view
            $table->decimal('v_offset', 6, 2)->default(0); // vertical centre offset
            $table->boolean('is_partial')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            $table->dropColumn(['haov', 'vaov', 'v_offset', 'is_partial']);
        });
    }
};
