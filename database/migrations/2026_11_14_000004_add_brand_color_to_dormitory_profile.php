<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each dorm can pick its own site color (Dormitory Profile > Dorm Logo).
 * Stored as one hex code like "#c98a2b"; App\Support\BrandTheme turns it
 * into the full set of light/dark shades. Null = the default NEST.PH green.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->string('brand_color', 7)->nullable()->after('brand_logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('brand_color');
        });
    }
};
