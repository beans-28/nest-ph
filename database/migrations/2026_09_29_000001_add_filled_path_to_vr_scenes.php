<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            // A copy of a phone panorama with a soft painted ceiling and floor
            // added, so visitors can look straight up/down without seeing
            // black. Null for true 360 photos (they don't need one).
            $table->string('filled_path')->nullable()->after('panorama_path');
        });
    }

    public function down(): void
    {
        Schema::table('vr_scenes', function (Blueprint $table) {
            $table->dropColumn('filled_path');
        });
    }
};
