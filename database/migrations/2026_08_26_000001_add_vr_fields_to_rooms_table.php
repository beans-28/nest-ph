<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'vr_caption')) {
                $table->string('vr_caption')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'vr_visibility')) {
                $table->string('vr_visibility')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'vr_visibility')) {
                $table->dropColumn('vr_visibility');
            }
            if (Schema::hasColumn('rooms', 'vr_caption')) {
                $table->dropColumn('vr_caption');
            }
        });
    }
};
