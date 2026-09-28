<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use Case Report — Manage Lease Contracts: "Expiring Soon" is a real
        // status a contract moves into, not just a UI label — steps 11.3 and
        // 14.1 both refer to updating status to/from it.
        $this->modifyColumn(
            "ALTER TABLE lease_contracts MODIFY status ENUM('pending','active','expiring_soon','expired','terminated') NOT NULL DEFAULT 'pending'",
            'lease_contracts', fn (Blueprint $t) => $t->enum('status', ['pending', 'active', 'expiring_soon', 'expired', 'terminated'])->default('pending')->change()
        );

        Schema::table('lease_contracts', function (Blueprint $table) {
            // Step 13: termination requires and stores a reason.
            $table->text('termination_reason')->nullable()->after('status');
            $table->timestamp('terminated_at')->nullable()->after('termination_reason');

            // Step 11.4: renewals are logged with admin + timestamp.
            $table->timestamp('last_renewed_at')->nullable()->after('terminated_at');
            $table->foreignId('last_renewed_by')->nullable()->after('last_renewed_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropForeign(['last_renewed_by']);
            $table->dropColumn(['termination_reason', 'terminated_at', 'last_renewed_at', 'last_renewed_by']);
        });

        $this->modifyColumn(
            "ALTER TABLE lease_contracts MODIFY status ENUM('pending','active','terminated') NOT NULL DEFAULT 'pending'",
            'lease_contracts', fn (Blueprint $t) => $t->enum('status', ['pending', 'active', 'terminated'])->default('pending')->change()
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
