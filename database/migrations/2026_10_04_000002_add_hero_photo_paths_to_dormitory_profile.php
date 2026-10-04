<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extra homepage slideshow photos (up to 4), shown after the cover photo
 * (logo_path) in the landing page hero carousel. 5 photos max in total.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->json('hero_photo_paths')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('hero_photo_paths');
        });
    }
};
