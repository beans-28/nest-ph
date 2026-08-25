<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penalty_id')->constrained('penalties')->cascadeOnDelete();
            $table->enum('action', ['created', 'waived', 'reinstated'])->default('created');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_audit_logs');
    }
};
