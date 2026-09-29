<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            // When the panorama picture itself was last uploaded. Unlike
            // updated_at, renaming a spot or moving arrows doesn't touch it,
            // so the public "Last updated on ..." line stays honest.
            $table->timestamp('photo_updated_at')->nullable()->after('filled_path');
        });

        // Existing photos: the best guess is when the spot was created.
        DB::table('vr_scenes')->update(['photo_updated_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            $table->dropColumn('photo_updated_at');
        });
    }
};
