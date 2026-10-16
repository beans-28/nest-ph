<?php

namespace Tests\Feature;

use App\Models\MaintenanceTicket;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\EmergencyDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Emergency tickets: online admins, auto-assigning Critical tickets to
 * the least busy online staff, several staff on one ticket, and the bell's
 * emergency pop-up data.
 */
class EmergencyTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['session.driver' => 'database']);
    }

    private function admin(bool $online = false): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->forceFill(['role_id' => Role::firstOrCreate(['role_name' => 'admin'])->id])->save();

        if ($online) {
            DB::table('sessions')->insert([
                'id' => Str::random(40), 'user_id' => $admin->id, 'ip_address' => '127.0.0.1',
                'user_agent' => 'test', 'payload' => '', 'last_activity' => now()->timestamp,
            ]);
        }

        return $admin;
    }

    private function ticket(string $priority = 'critical'): MaintenanceTicket
    {
        $tenant = Tenant::create(['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => fake()->unique()->safeEmail()]);

        return MaintenanceTicket::create([
            'tenant_id' => $tenant->id,
            'title' => 'Sparks from the outlet',
            'category' => 'electrical_issue',
            'description' => 'It sparked.',
            'status' => 'open',
            'priority' => $priority,
        ]);
    }

    public function test_only_recently_active_admins_are_online(): void
    {
        $online = $this->admin(true);
        $idle = $this->admin();
        DB::table('sessions')->insert([
            'id' => Str::random(40), 'user_id' => $idle->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'test',
            'payload' => '', 'last_activity' => now()->subMinutes(EmergencyDispatcher::ONLINE_MINUTES + 1)->timestamp,
        ]);

        $this->assertEquals([$online->id], EmergencyDispatcher::onlineAdminIds()->all());
    }

    public function test_critical_ticket_goes_to_the_least_busy_online_admins(): void
    {
        $busy = $this->admin(true);
        $free1 = $this->admin(true);
        $free2 = $this->admin(true);
        $this->admin(); // offline, never picked
        $this->ticket()->assignees()->attach($busy->id);

        $ticket = $this->ticket();
        $this->assertSame(EmergencyDispatcher::TEAM_SIZE, EmergencyDispatcher::autoAssign($ticket));

        $team = $ticket->assignees()->pluck('users.id')->sort()->values()->all();
        $this->assertEquals(collect([$free1->id, $free2->id])->sort()->values()->all(), $team);
        $this->assertTrue((bool) $ticket->assignees()->first()->pivot->auto_assigned);
        $this->assertContains($ticket->fresh()->assigned_to, $team);
    }

    public function test_non_critical_or_nobody_online_assigns_no_one(): void
    {
        $this->admin(true);
        $this->assertSame(0, EmergencyDispatcher::autoAssign($this->ticket('high')));

        DB::table('sessions')->delete();
        $this->assertSame(0, EmergencyDispatcher::autoAssign($this->ticket()));
    }

    public function test_admin_can_put_several_staff_on_one_ticket(): void
    {
        [$a, $b, $c] = [$this->admin(), $this->admin(), $this->admin()];
        $ticket = $this->ticket();

        $this->actingAs($a)->patchJson("/tickets/{$ticket->id}", [
            'status' => 'in_progress',
            'assignee_ids' => [$a->id, $b->id, $c->id],
        ])->assertOk()->assertJsonCount(3, 'ticket.assignees');

        // Unticking everyone leaves the ticket unassigned.
        $this->actingAs($a)->patchJson("/tickets/{$ticket->id}", [
            'status' => 'in_progress',
            'assignee_ids' => [],
        ])->assertOk()->assertJsonCount(0, 'ticket.assignees');
        $this->assertNull($ticket->fresh()->assigned_to);
    }

    public function test_tenants_cannot_be_assigned(): void
    {
        $admin = $this->admin();
        $notAdmin = User::factory()->create();

        $this->actingAs($admin)->patchJson("/tickets/{$this->ticket()->id}", [
            'status' => 'open',
            'assignee_ids' => [$notAdmin->id],
        ])->assertStatus(422);
    }

    public function test_bell_pops_up_my_emergencies_and_unassigned_ones(): void
    {
        $me = $this->admin();
        $other = $this->admin();
        $this->ticket()->assignees()->attach($me->id);
        $this->ticket()->assignees()->attach($other->id); // someone else's: not shown to me
        $this->ticket(); // no one assigned

        $res = $this->actingAs($me)->getJson('/admin/notifications')->assertOk();
        $labels = collect($res->json('items'))->pluck('label');

        $this->assertContains('1 emergency assigned to you', $labels);
        $this->assertContains('1 emergency with no one assigned', $labels);
        $this->assertCount(2, $res->json('emergencies'));
    }
}
