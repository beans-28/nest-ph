<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tenants can now submit their own notarized copy, so each copy needs a
 * review state, like a proof of payment:
 *   none      -> nothing on file
 *   submitted -> tenant uploaded it, waiting for admin review
 *   approved  -> admin checked it (or uploaded it); requirement met
 *   rejected  -> admin sent it back with a reason; tenant re-uploads
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->string('notarized_status', 20)->default('none')->after('notarized_at');
            $table->string('notarized_rejection_reason', 255)->nullable()->after('notarized_status');
            $table->timestamp('notarized_reviewed_at')->nullable()->after('notarized_rejection_reason');
            $table->foreignId('notarized_reviewed_by')->nullable()->after('notarized_reviewed_at')
                ->constrained('users')->nullOnDelete();
        });

        // Copies uploaded before this change were uploaded by an admin, so
        // they count as already checked.
        DB::table('lease_contracts')
            ->whereNotNull('notarized_document_path')
            ->update(['notarized_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropForeign(['notarized_reviewed_by']);
            $table->dropColumn([
                'notarized_status',
                'notarized_rejection_reason',
                'notarized_reviewed_at',
                'notarized_reviewed_by',
            ]);
        });
    }
};
