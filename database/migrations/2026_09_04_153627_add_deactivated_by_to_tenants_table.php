<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Table 38, postcondition: "Log the deactivation action with
            // administrator ID, reason, and timestamp." Reason and timestamp
            // already existed (deactivation_reason/deactivated_at) -- the
            // admin ID itself was the missing piece.
            $table->foreignId('deactivated_by')->nullable()->after('deactivated_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deactivated_by');
        });
    }
};