<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Cash payments recorded by an admin are 'approved' immediately.
            // Tenant-submitted online proofs start as 'pending' and only count
            // toward the balance once an admin approves them.
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved')->after('payment_date');

            $table->string('proof_path', 255)->nullable()->after('status');
            $table->string('review_notes', 500)->nullable()->after('proof_path');
            $table->foreignId('reviewed_by')->nullable()->after('review_notes')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'proof_path', 'review_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};
