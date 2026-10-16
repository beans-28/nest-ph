<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A ticket can now have several admins on it at once (e.g. two staff
     * sent to a fire alarm). maintenance_tickets.assigned_to stays as the
     * "lead" (first assignee) so older screens and reports keep working.
     * auto_assigned = picked by the system for an emergency, not by hand.
     */
    public function up(): void
    {
        Schema::create('maintenance_ticket_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('maintenance_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('auto_assigned')->default(false);
            $table->timestamps();
            $table->unique(['ticket_id', 'user_id']);
        });

        // Carry over the single assignee every existing ticket already has.
        DB::table('maintenance_tickets')->whereNotNull('assigned_to')->orderBy('id')->each(function ($t) {
            DB::table('maintenance_ticket_assignees')->insert([
                'ticket_id' => $t->id,
                'user_id' => $t->assigned_to,
                'auto_assigned' => false,
                'created_at' => $t->updated_at ?? now(),
                'updated_at' => $t->updated_at ?? now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_ticket_assignees');
    }
};
