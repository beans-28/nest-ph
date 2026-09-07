<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dormitory_amenities', function (Blueprint $table) {
            $table->id();
            // A short machine-readable key ('wifi', 'kitchen', ...) so the
            // admin page and public page can both look up the right icon
            // without string-matching on the label text.
            $table->string('key', 40)->unique();
            $table->string('label', 60);
            $table->boolean('is_enabled')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed the fixed set of amenities shown in the Dormitory Profile
        // design (Manage Dormitory Profile page). This is a toggle list, not
        // a free-add list -- the admin turns amenities on/off, they don't
        // create new ones -- so seeding them here means the admin page has
        // something to show the moment this migration runs, no separate
        // seeder command needed.
        $now = now();
        DB::table('dormitory_amenities')->insert([
            ['key' => 'wifi', 'label' => 'WiFi', 'is_enabled' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'kitchen', 'label' => 'Kitchen', 'is_enabled' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'study_area', 'label' => 'Study Area', 'is_enabled' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'parking_area', 'label' => 'Parking Area', 'is_enabled' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cctv', 'label' => 'CCTV', 'is_enabled' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hot_cold_shower', 'label' => 'Hot & Cold Shower', 'is_enabled' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'laundry_area', 'label' => 'Laundry Area', 'is_enabled' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['key' => '24_7_security', 'label' => '24/7 Security', 'is_enabled' => true, 'sort_order' => 8, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dormitory_amenities');
    }
};
