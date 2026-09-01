<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {
            // The table only ever had created_at -- a pending -> sent
            // transition (Table 24's retry exception path) left no trace of
            // when it happened. Standard Eloquent convention, low-risk.
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
