<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payments and Fees Schedule 3.2: a reservation is valid for one month from
 * the date of payment. When a half-paid move-in fee runs past that, the
 * reservations:expire command releases the bed and stamps this column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->timestamp('reservation_expired_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropColumn('reservation_expired_at');
        });
    }
};
