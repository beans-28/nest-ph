<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Billing & Payments settings:
 *   - Each bill now keeps the grace period and late penalty % that applied
 *     when it was created, so changing the settings only affects future
 *     bills (never old ones).
 *   - The move-in fee records its advance rent and security deposit
 *     separately, so the deposit is never treated as rent.
 *   - The dorm can choose how monthly due dates work: a fixed day of the
 *     month, or the same day as the tenant's start date.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('billing_statements', 'grace_period_days')) {
            Schema::table('billing_statements', function (Blueprint $table) {
                $table->unsignedTinyInteger('grace_period_days')->nullable()->after('due_date');
                $table->decimal('late_penalty_percent', 5, 2)->nullable()->after('grace_period_days');
                $table->decimal('advance_amount', 10, 2)->nullable()->after('base_rent');
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('advance_amount');
            });
        }

        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->string('rent_due_basis', 12)->default('fixed_day')->after('rent_due_day'); // fixed_day | start_date
        });

        // Existing bills: stamp them with today's settings, which are the
        // rules they've been following so far.
        $profile = DB::table('dormitory_profile')->first();
        DB::table('billing_statements')->update([
            'grace_period_days' => $profile->grace_period_days ?? 3,
            'late_penalty_percent' => $profile->late_penalty_percent ?? 10,
        ]);

        // Existing move-in fees were always 1 month advance + 1 month deposit.
        DB::table('billing_statements')->where('type', 'move_in')->update([
            'advance_amount' => DB::raw('ROUND(base_rent / 2, 2)'),
            'deposit_amount' => DB::raw('base_rent - ROUND(base_rent / 2, 2)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn(['grace_period_days', 'late_penalty_percent', 'advance_amount', 'deposit_amount']);
        });

        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn('rent_due_basis');
        });
    }
};
