<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY tenant_type VARCHAR(30) NULL",
            'tenants', fn (Blueprint $t) => $t->string('tenant_type', 30)->nullable()->change()
        );
    }

    public function down(): void
    {
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY tenant_type ENUM('student','employee','transient_worker') NULL",
            'tenants', fn (Blueprint $t) => $t->enum('tenant_type', ['student', 'employee', 'transient_worker'])->nullable()->change()
        );
    }

    /**
     * Runs the original MySQL ALTER unchanged on MySQL/MariaDB (so existing
     * databases behave exactly as before). Any other database -- the
     * in-memory SQLite that `php artisan test` uses -- doesn't understand
     * MODIFY, so it gets the same column change through Laravel's portable
     * ->change() instead (built into Laravel 11+, no doctrine/dbal needed).
     */
    private function modifyColumn(string $mysqlSql, string $table, Closure $change): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement($mysqlSql);

            return;
        }

        Schema::table($table, $change);
    }
};
