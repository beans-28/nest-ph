<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('billing_id')->nullable()->constrained('billing_statements')->nullOnDelete();
            $table->unsignedTinyInteger('stage')->default(1);
            $table->string('action_type', 50)->nullable();
            $table->text('message_content')->nullable();
            $table->enum('status', ['pending', 'sent', 'resolved'])->default('pending');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
    }
};
