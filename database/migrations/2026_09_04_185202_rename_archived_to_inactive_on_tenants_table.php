<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Table 38 names the deactivated state "Inactive," but the original
        // enum (predating Tenant Manager) used "archived." Renaming in three
        // steps so existing rows never sit on an invalid value mid-migration:
        // widen the enum to allow both, move the data, then narrow it.
        DB::statement("ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived','inactive') NOT NULL DEFAULT 'pending_move_in_payment'");
        DB::statement("UPDATE tenants SET status = 'inactive' WHERE status = 'archived'");
        DB::statement("ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','inactive') NOT NULL DEFAULT 'pending_move_in_payment'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived','inactive') NOT NULL DEFAULT 'pending_move_in_payment'");
        DB::statement("UPDATE tenants SET status = 'archived' WHERE status = 'inactive'");
        DB::statement("ALTER TABLE tenants MODIFY status ENUM('pending_move_in_payment','active','archived') NOT NULL DEFAULT 'pending_move_in_payment'");
    }
};