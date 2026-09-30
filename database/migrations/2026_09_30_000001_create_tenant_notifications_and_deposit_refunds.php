<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tenant notification panel (v39): bill reminders, payment results,
        // tickets, announcements, penalties, lease and deposit updates.
        Schema::create('tenant_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('type', 40);          // e.g. bill_due, payment_approved
            $table->string('title', 160);
            $table->text('body')->nullable();
            $table->string('link', 255)->nullable();
            // Stops the same automatic reminder being created twice,
            // e.g. "bill_due:42". Null for one-off event notifications.
            $table->string('dedupe_key', 120)->nullable()->unique();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'read_at']);
        });

        // Security deposit refund at move-out (v39). The deposit itself is
        // half of the paid move-in fee (1 month deposit + 1 month advance);
        // this records what was given back and what was deducted.
        Schema::create('deposit_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('deductions_amount', 10, 2)->default(0);
            $table->string('deductions_note', 500)->nullable();
            $table->decimal('refund_amount', 10, 2);
            $table->string('refund_method', 60);
            $table->string('reference_number', 100)->nullable();
            $table->date('refunded_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_refunds');
        Schema::dropIfExists('tenant_notifications');
    }
};
