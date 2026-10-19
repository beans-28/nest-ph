<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            // How many months of rent the move-in fee collects as advance rent
            // and as security deposit. 1 + 1 matches the signed documents.
            $table->unsignedTinyInteger('advance_rent_months')->default(1)->after('deposit_refund_days');
            $table->unsignedTinyInteger('security_deposit_months')->default(1)->after('advance_rent_months');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->dropColumn(['advance_rent_months', 'security_deposit_months']);
        });
    }
};
