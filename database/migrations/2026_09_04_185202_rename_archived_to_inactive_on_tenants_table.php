<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Table 37 names the deactivated state "Inactive," but the original
        // enum (predating Tenant Manager) used "archived." Renaming in three
        // steps so existing rows never sit on an invalid value mid-migration:
        // widen the enum to allow both, move the data, then narrow it.
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived','inactive') NOT NULL DEFAULT 'pending_move_in_payment'",
            'tenants', fn (Blueprint $t) => $t->enum('status', ['pending_move_in_payment', 'active', 'archived', 'inactive'])->default('pending_move_in_payment')->change()
        );
        DB::statement("UPDATE tenants SET status = 'inactive' WHERE status = 'archived'");
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','inactive') NOT NULL DEFAULT 'pending_move_in_payment'",
            'tenants', fn (Blueprint $t) => $t->enum('status', ['pending_move_in_payment', 'active', 'inactive'])->default('pending_move_in_payment')->change()
        );
    }

    public function down(): void
    {
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived','inactive') NOT NULL DEFAULT 'pending_move_in_payment'",
            'tenants', fn (Blueprint $t) => $t->enum('status', ['pending_move_in_payment', 'active', 'archived', 'inactive'])->default('pending_move_in_payment')->change()
        );
        DB::statement("UPDATE tenants SET status = 'archived' WHERE status = 'inactive'");
        $this->modifyColumn(
            "ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived') NOT NULL DEFAULT 'pending_move_in_payment'",
            'tenants', fn (Blueprint $t) => $t->enum('status', ['pending_move_in_payment', 'active', 'archived'])->default('pending_move_in_payment')->change()
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
