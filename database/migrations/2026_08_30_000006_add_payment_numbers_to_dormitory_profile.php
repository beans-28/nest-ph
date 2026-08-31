<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // Displayed on the move-in payment QR cards (GCash / BDO).
            // Nothing stored these anywhere before — the seeded
            // payments_and_fees text only mentions the payment methods
            // generically, no actual account numbers.
            $table->string('gcash_number', 30)->nullable()->after('contract_template_path');
            $table->string('bdo_account_number', 30)->nullable()->after('gcash_number');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn(['gcash_number', 'bdo_account_number']);
        });
    }
};
