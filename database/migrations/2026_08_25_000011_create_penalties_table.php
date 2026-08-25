<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // Nullable: set only when this penalty originated from a recorded
            // damage. Null means it was added manually by an admin.
            $table->foreignId('damage_id')->nullable()->constrained('damages')->nullOnDelete();

            // Nullable: filled in once this penalty gets pulled into a
            // billing statement as a line item (Week 5, Tue's task).
            $table->foreignId('billing_id')->nullable()->constrained('billing_statements')->nullOnDelete();

            $table->enum('type', ['damage', 'manual', 'other'])->default('manual');
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['active', 'waived'])->default('active');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
