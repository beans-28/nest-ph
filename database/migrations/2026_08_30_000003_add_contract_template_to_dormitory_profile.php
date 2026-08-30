<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // Use Case Report — Apply for Occupancy, steps 4–5: the standard
            // dormitory contract applicants review and download before
            // signing. Separate from policies_file_path (house rules /
            // payment schedule shown on the Dorm Info page) — this is the
            // actual lease/terms document.
            $table->string('contract_template_path', 255)->nullable()->after('policies_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('contract_template_path');
        });
    }
};
