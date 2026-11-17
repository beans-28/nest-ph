<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Page background color (Dormitory Profile > Background Color). Only the
 * light neutral presets in App\Support\BrandTheme::BACKGROUNDS are allowed.
 * Null = each page's own default background.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->string('background_color', 7)->nullable()->after('brand_color');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('background_color');
        });
    }
};
