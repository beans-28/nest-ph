<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Floor area in square meters, shown on the public listings so
            // applicants can compare room sizes. Optional for older rooms.
            $table->decimal('area_sqm', 6, 2)->nullable()->after('amenities');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('area_sqm');
        });
    }
};
