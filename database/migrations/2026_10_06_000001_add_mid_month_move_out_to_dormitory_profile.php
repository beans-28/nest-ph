<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // How the last month is billed when a contract ends mid-month.
            // prorated = only the days stayed (the old behavior); full = whole month.
            $table->string('mid_month_move_out', 10)->default('prorated')->after('mid_month_move_in');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('mid_month_move_out');
        });
    }
};
