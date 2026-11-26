<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Where We Are" section on the dorm homepage, set from the Dormitory Profile:
 * - map_location: what the map pins (a place name, an address, or
 *   "latitude, longitude" copied from Google Maps). Empty = name + address.
 * - location_note: the short "how to get here" line beside the map.
 * - show_location_map: hide the whole section if the dorm doesn't want it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->string('map_location', 255)->nullable()->after('address');
            $table->string('location_note', 300)->nullable()->after('map_location');
            $table->boolean('show_location_map')->default(true)->after('location_note');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn(['map_location', 'location_note', 'show_location_map']);
        });
    }
};
