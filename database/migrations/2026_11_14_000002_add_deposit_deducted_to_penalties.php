<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pureza revision (2026-11-14): penalties are paid from the tenant's
 * security deposit first. `deposit_deducted` is how much of the penalty
 * the deposit covered; only the rest (amount - deposit_deducted) is
 * charged on a bill. 0 means the normal flow (all of it is billed).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            $table->decimal('deposit_deducted', 10, 2)->default(0)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            $table->dropColumn('deposit_deducted');
        });
    }
};
