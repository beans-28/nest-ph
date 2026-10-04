<?php

namespace Tests\Feature;

use App\Models\MaintenanceTicket;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tickets: auto priority, response/resolution deadlines, "due soon"
 * warnings, statuses (Pending / In Progress / Resolved / Closed) and
 * delays with a revised resolution date.
 */
class TicketDeadlinesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        return $admin;
    }

    private function ticket(array $attrs = [], int $hoursAgo = 0): MaintenanceTicket
    {
        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => fake()->unique()->safeEmail()]);

        $ticket = MaintenanceTicket::create($attrs + [
            'tenant_id' => $tenant->id,
            'title' => 'Double charge',
            'category' => 'billing_payment_concern',
            'description' => 'I was charged twice.',
            'status' => 'open',
            'priority' => 'medium',
        ]);
        $ticket->created_at = now()->subHours($hoursAgo);
        $ticket->save();

        return $ticket->fresh();
    }

    public function test_ticket_is_due_soon_in_the_last_quarter_of_its_window(): void
    {
        // Medium: respond within 8 hours. 7 hours in = 1 hour left (< 25%).
        $ticket = $this->ticket([], 7);

        $this->assertTrue($ticket->isDueSoon());
        $this->assertFalse($ticket->isOverdue());
        $this->assertSame(1, MaintenanceTicket::overdueSummary()['due_soon']);
    }

    public function test_ticket_with_no_response_past_target_is_overdue(): void
    {
        $ticket = $this->ticket([], 9);

        $this->assertTrue($ticket->isResponseOverdue());
        $this->assertTrue($ticket->isOverdue());
        $this->assertFalse($ticket->isDueSoon());
    }

    public function test_admin_bell_lists_tickets_nearing_their_deadline(): void
    {
        $this->ticket([], 7);

        $labels = collect($this->actingAs($this->admin())->getJson('/admin/notifications')->assertOk()->json('items'))
            ->pluck('label');

        $this->assertContains('1 ticket nearing its deadline', $labels);
    }

    public function test_delay_needs_a_reason_and_a_future_date(): void
    {
        $ticket = $this->ticket(['status' => 'in_progress']);

        $this->actingAs($this->admin())->patchJson("/tickets/{$ticket->id}", [
            'status' => 'in_progress', 'delayed' => true, 'delay_reason' => '', 'revised_due_at' => now()->subDay()->toDateTimeString(),
        ])->assertStatus(422)->assertJsonValidationErrors(['delay_reason', 'revised_due_at']);
    }

    public function test_recording_a_delay_moves_the_deadline_and_tells_the_tenant(): void
    {
        // 4 days old Medium ticket: past its normal 3-day target.
        $ticket = $this->ticket(['status' => 'in_progress', 'responded_at' => now()->subDays(4)], 96);
        $this->assertTrue($ticket->isResolutionOverdue());

        $revised = now()->addDays(2)->startOfMinute();
        $res = $this->actingAs($this->admin())->patchJson("/tickets/{$ticket->id}", [
            'status' => 'in_progress', 'delayed' => true,
            'delay_reason' => 'Waiting for a replacement part', 'revised_due_at' => $revised->format('Y-m-d\TH:i'),
        ])->assertOk();

        $this->assertTrue($res->json('ticket.is_delayed'));
        $this->assertFalse($res->json('ticket.is_overdue'));
        $this->assertTrue($ticket->fresh()->resolveDueAt()->equalTo($revised));
        $this->assertDatabaseHas('tenant_notifications', [
            'tenant_id' => $ticket->tenant_id, 'title' => 'Your ticket "Double charge" is delayed',
        ]);

        // Unticking "delayed" goes back to the normal target.
        $this->actingAs($this->admin())->patchJson("/tickets/{$ticket->id}", ['status' => 'in_progress', 'delayed' => false])->assertOk();
        $this->assertNull($ticket->fresh()->revised_due_at);
    }

    public function test_closed_tickets_have_no_deadline(): void
    {
        $ticket = $this->ticket([], 200);

        $res = $this->actingAs($this->admin())->patchJson("/tickets/{$ticket->id}", ['status' => 'closed'])->assertOk();

        $this->assertSame('Closed', $res->json('ticket.status_label'));
        $this->assertFalse($res->json('ticket.is_overdue'));
        $this->assertNull($res->json('ticket.deadline_label'));
        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    public function test_old_statuses_are_rejected(): void
    {
        $ticket = $this->ticket();

        $this->actingAs($this->admin())->patchJson("/tickets/{$ticket->id}", ['status' => 'seen'])
            ->assertStatus(422);
    }
}
