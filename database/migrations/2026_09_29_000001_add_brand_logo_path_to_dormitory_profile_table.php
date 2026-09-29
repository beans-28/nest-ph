<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // The dorm's own square logo (sidebar, login page, browser tab,
            // emails). Separate from logo_path, which is actually used as the
            // wide cover photo. Under the Data Privacy Act the dorm is the
            // PIC and NEST.PH the PIP, so both marks need to be visible.
            $table->string('brand_logo_path', 255)->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('brand_logo_path');
        });
    }
};
