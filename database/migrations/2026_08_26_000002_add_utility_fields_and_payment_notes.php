<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->decimal('utilities_amount', 10, 2)->default(0)->after('base_rent');
            $table->decimal('wifi_amount', 10, 2)->default(0)->after('utilities_amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Tenant-submitted note (e.g. time of payment) captured on the
            // proof-of-payment form. Distinct from `review_notes`, which is
            // the admin's note written when approving/rejecting.
            $table->string('notes', 500)->nullable()->after('proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn(['utilities_amount', 'wifi_amount']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
