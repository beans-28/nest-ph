<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            // pay_ahead: a tenant paying future months of RENT early (not
            // utilities, which change month to month).
            $table->enum('type', ['move_in', 'monthly', 'pay_ahead'])->default('monthly')->change();
            // The part of a monthly bill's rent covered by a paid pay-ahead.
            // base_rent is what's still due; this is only for display.
            $table->decimal('prepaid_rent', 10, 2)->default(0)->after('base_rent');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn('prepaid_rent');
            $table->enum('type', ['move_in', 'monthly'])->default('monthly')->change();
        });
    }
};
