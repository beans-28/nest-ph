<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ticket statuses become Pending / In Progress / Resolved / Closed.
     * The stored key for Pending stays "open" (only the label changes) so
     * nothing else that counts open tickets has to change.
     *   seen     -> in_progress (staff had already looked at it)
     *   rejected -> closed
     *
     * Delays: when a ticket can't meet its resolution target because of
     * something outside staff control, admins record why and a revised
     * expected resolution date, which replaces the normal target.
     */
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('status', 20)->default('open')->change();
            $table->text('delay_reason')->nullable()->after('responded_at');
            $table->timestamp('revised_due_at')->nullable()->after('delay_reason');
        });

        DB::table('maintenance_tickets')->where('status', 'seen')->update(['status' => 'in_progress']);
        DB::table('maintenance_tickets')->where('status', 'rejected')->update(['status' => 'closed']);

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open')->change();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('status', 20)->default('open')->change();
            $table->dropColumn(['delay_reason', 'revised_due_at']);
        });

        DB::table('maintenance_tickets')->where('status', 'closed')->update(['status' => 'rejected']);

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'seen', 'in_progress', 'resolved', 'rejected'])->default('open')->change();
        });
    }
};
