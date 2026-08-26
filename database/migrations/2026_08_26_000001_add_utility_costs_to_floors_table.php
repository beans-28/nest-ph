<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            // Fixed monthly cost for this floor, split evenly among its
            // currently-active tenants at billing time. Matches Use Case
            // Report Table 19: "Add the fixed utility charges among tenants
            // sharing the same floor/facility." Nullable/defaults to 0 —
            // an admin hasn't necessarily configured these yet.
            $table->decimal('monthly_utility_cost', 10, 2)->default(0)->after('floor_name');
            $table->decimal('monthly_wifi_cost', 10, 2)->default(0)->after('monthly_utility_cost');
        });
    }

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn(['monthly_utility_cost', 'monthly_wifi_cost']);
        });
    }
};
