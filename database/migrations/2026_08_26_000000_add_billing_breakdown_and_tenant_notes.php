<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            // Itemized breakdown for the new Billing & Payments screen (Rent /
            // Utilities / Wi-Fi bars). Both default to 0 so every existing
            // statement — and the admin's existing generateForContract() flow,
            // which never sets these — keeps working unchanged.
            $table->decimal('utilities_amount', 10, 2)->default(0)->after('base_rent');
            $table->decimal('wifi_amount', 10, 2)->default(0)->after('utilities_amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Free-text field for the tenant-facing "Time of Payment" +
            // "Notes (optional)" inputs on the proof-of-payment form. Distinct
            // from the existing admin-only review_notes column.
            $table->string('tenant_notes', 500)->nullable()->after('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn(['utilities_amount', 'wifi_amount']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('tenant_notes');
        });
    }
};
