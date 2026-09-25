<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Whole-room monthly costs, split evenly among the room's
            // active tenants each time a bill is generated. Replaces the
            // floor-level columns, which no screen ever let an admin set.
            $table->decimal('monthly_utility_cost', 10, 2)->default(0)->after('monthly_rate');
            $table->decimal('monthly_wifi_cost', 10, 2)->default(0)->after('monthly_utility_cost');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['monthly_utility_cost', 'monthly_wifi_cost']);
        });
    }
};
