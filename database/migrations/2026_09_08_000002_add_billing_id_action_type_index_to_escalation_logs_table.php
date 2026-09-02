<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {
            // Week 6, Tue (PELEA): "Add indexes to support a fast 'all
            // overdue tenants past X days' query." Only single-column FK
            // indexes existed before (tenant_id, billing_id, performed_by).
            // EscalationService::findLog() -- called on every single stage
            // check, for every overdue billing statement, on every run of
            // `escalation:process` -- does exactly this lookup:
            //   WHERE billing_id = ? AND action_type = ?
            // A composite index here lets that resolve directly instead of
            // scanning every log row for a given billing_id.
            $table->index(['billing_id', 'action_type'], 'escalation_logs_billing_id_action_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {
            $table->dropIndex('escalation_logs_billing_id_action_type_index');
        });
    }
};
