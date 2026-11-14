<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pureza revisions (2026-11-14):
 * - A 2x2 ID picture uploaded on the application, kept on the tenant
 *   record and used as their profile picture.
 * - Security deposits are only refundable after the tenant has stayed
 *   longer than a set number of months (Pureza: 6).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('id_photo_path')->nullable()->after('id_document_path');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('id_photo_path')->nullable()->after('id_document_path');
        });
        Schema::table('dormitory_profile', function (Blueprint $table) {
            $table->unsignedSmallInteger('deposit_refundable_after_months')->default(6)->after('deposit_refund_days');
        });
        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->boolean('forfeited')->default(false)->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('applications', fn (Blueprint $t) => $t->dropColumn('id_photo_path'));
        Schema::table('tenants', fn (Blueprint $t) => $t->dropColumn('id_photo_path'));
        Schema::table('dormitory_profile', fn (Blueprint $t) => $t->dropColumn('deposit_refundable_after_months'));
        Schema::table('deposit_refunds', fn (Blueprint $t) => $t->dropColumn('forfeited'));
    }
};
