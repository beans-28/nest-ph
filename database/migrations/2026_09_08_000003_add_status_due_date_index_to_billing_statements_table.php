<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            // Week 6, Tue (PELEA): "Add indexes to support a fast 'all
            // overdue tenants past X days' query."
            //
            // The existing (tenant_id, status) index from Week 5 serves
            // "show me one tenant's bills" -- it doesn't help
            // EscalationService::processAll(), which does the opposite
            // query, across every tenant at once:
            //   WHERE status = 'overdue'
            // ...then sorts/filters by due_date to compute days overdue.
            // tenant_id isn't part of that filter at all, so the old index
            // can't be used to serve it. This composite index leads with
            // status (the actual filter column) and includes due_date so
            // the days-overdue calculation doesn't need a second lookup.
            $table->index(['status', 'due_date'], 'billing_statements_status_due_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropIndex('billing_statements_status_due_date_index');
        });
    }
};
