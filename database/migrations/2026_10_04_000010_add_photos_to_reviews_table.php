<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional photos a former tenant attaches to their review (up to 3).
     * Stored as a JSON list of paths on the public disk.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->json('photos')->nullable()->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('photos');
        });
    }
};
