<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a tenant report another tenant through the Tickets feature.
     *   - new "tenant_report" category
     *   - reported_tenant_id: who is being reported
     *   - report_reason: one of MaintenanceTicket::REPORT_REASONS
     */
    private const OLD_CATEGORIES = [
        'billing_payment_concern',
        'electrical_issue',
        'plumbing_water_emergency',
        'security_concern',
        'structural_damage',
        'safety_security',
        'fire_safety_hazard',
        'maintenance_repairs',
        'facilities_amenities',
        'administrative_leasing_concern',
        'account_access_issue',
        'noise_roommate_concern',
        'suggestion_feedback',
    ];

    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('category', [...self::OLD_CATEGORIES, 'tenant_report'])->change();
            $table->foreignId('reported_tenant_id')->nullable()->after('bed_id')
                ->constrained('tenants')->nullOnDelete();
            $table->string('report_reason', 50)->nullable()->after('reported_tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reported_tenant_id');
            $table->dropColumn('report_reason');
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('category', self::OLD_CATEGORIES)->change();
        });
    }
};
