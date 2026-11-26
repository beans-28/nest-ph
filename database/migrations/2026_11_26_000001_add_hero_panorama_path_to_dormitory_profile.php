<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional 360° photo for the homepage (e.g. the lobby). When set, the
 * landing page shows it as a drag-to-look-around view instead of the
 * photo slideshow.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->string('hero_panorama_path')->nullable()->after('hero_photo_paths');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('hero_panorama_path');
        });
    }
};
