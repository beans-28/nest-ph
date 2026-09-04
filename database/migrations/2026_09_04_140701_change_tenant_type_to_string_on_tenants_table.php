<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Application::TENANT_TYPES (student/working_student/full_time_employee/
        // part_time_employee) uses a different vocabulary than the 3-value enum
        // Table 15's Add New Tenant flow used. Tenants created from an approved
        // online Application now copy type_of_tenant over (see
        // ApplicationController::createTenantWithLogin()), and a strict enum
        // would reject those values outright. Widened to a plain string rather
        // than keeping two enums in sync — flagged to BAGUI as a genuine
        // vocabulary mismatch between Table 13 and Table 15 worth reconciling.
        DB::statement("ALTER TABLE tenants MODIFY tenant_type VARCHAR(30) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tenants MODIFY tenant_type ENUM('student','employee','transient_worker') NULL");
    }
};