<?php

use App\Services\TicketPriorityClassifier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replaces the admin-set Urgent / Non-Urgent tag with four levels the
     * system assigns itself (critical / high / medium / low), each with a
     * response and resolution target (see MaintenanceTicket::SLA).
     *   - priority_reason: short "why" shown to admins
     *   - responded_at: first staff response (status change or reply),
     *     used for the "Initial Response" target
     * Existing tickets are re-scored by TicketPriorityClassifier.
     */
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('priority', 20)->nullable()->change();
            $table->string('priority_reason', 120)->nullable()->after('priority');
            $table->timestamp('responded_at')->nullable()->after('assigned_to');
        });

        DB::table('maintenance_tickets')->orderBy('id')->each(function ($t) {
            $result = TicketPriorityClassifier::classify($t->category, $t->title, $t->description, $t->report_reason);

            $firstReply = DB::table('ticket_replies')
                ->where('ticket_id', $t->id)
                ->whereNull('tenant_id')
                ->min('created_at');

            DB::table('maintenance_tickets')->where('id', $t->id)->update([
                'priority' => $result['priority'],
                'priority_reason' => $result['reason'],
                // Best guess for old tickets: first staff reply, else the
                // last update if staff already moved it past "open".
                'responded_at' => $firstReply ?? ($t->status !== 'open' ? $t->updated_at : null),
            ]);
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('priority', TicketPriorityClassifier::LEVELS)->default('medium')->change();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('priority', 20)->nullable()->change();
        });

        DB::table('maintenance_tickets')->whereIn('priority', ['critical', 'high'])->update(['priority' => 'urgent']);
        DB::table('maintenance_tickets')->whereIn('priority', ['medium', 'low'])->update(['priority' => 'non_urgent']);

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('priority', ['urgent', 'non_urgent'])->nullable()->change();
            $table->dropColumn(['priority_reason', 'responded_at']);
        });
    }
};
