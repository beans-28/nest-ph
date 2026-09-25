<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Every dormitory accepts cash at the office, so one cash method is
     * always present. PaymentMethodController refuses to delete it or
     * change its type; the admin can still rename it or edit the
     * instructions tenants see.
     */
    public function up(): void
    {
        if (DB::table('payment_methods')->where('type', 'cash')->exists()) {
            return;
        }

        DB::table('payment_methods')->insert([
            'type' => 'cash',
            'name' => 'Cash Payment',
            'instructions' => 'Pay in person at the lobby / admin office.',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Left in place: removing it would strand payments recorded as cash.
    }
};
