<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            // Week 5, Thu (PELEA): "Index the billing table on tenant_id +
            // status for faster dashboard queries." Same pattern already
            // used on penalties (penalties_tenant_id_status_index) — this
            // table never actually got its equivalent. Speeds up exactly
            // the kind of query the admin Billing Overview / Pending
            // Payment tabs and BillingStatement::syncOverdueStatuses() run
            // constantly: "all of tenant X's unpaid/overdue statements."
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
        });
    }
};
