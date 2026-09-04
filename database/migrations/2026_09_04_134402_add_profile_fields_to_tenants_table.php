<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Use Case Reports — Manage Tenant Records (Table 14) and Add
            // New Tenant (Table 15) both call for these on the tenant's own
            // record. They already exist on `applications` for online
            // applicants, but a tenant created directly by an admin
            // (walk-in, Table 15) has no application row to pull them from
            // — so they're duplicated here rather than requiring a join
            // that wouldn't exist for every tenant.
            $table->date('date_of_birth')->nullable()->after('emergency_contact_number');
            $table->string('home_address', 255)->nullable()->after('date_of_birth');
            $table->enum('tenant_type', ['student', 'employee', 'transient_worker'])->nullable()->after('home_address');
            $table->string('id_document_path')->nullable()->after('tenant_type');
            $table->string('signed_contract_path')->nullable()->after('id_document_path');

            // Table 38 — Deactivate Tenant Account.
            $table->string('deactivation_reason', 500)->nullable()->after('status');
            $table->timestamp('deactivated_at')->nullable()->after('deactivation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'home_address',
                'tenant_type',
                'id_document_path',
                'signed_contract_path',
                'deactivation_reason',
                'deactivated_at',
            ]);
        });
    }
};