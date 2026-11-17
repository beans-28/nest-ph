<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotifyOverdueApplications;
use App\Http\Middleware\EnsureAdminHasPrivilege;
use App\Models\Application;
use App\Models\BillingStatement;
use App\Models\Inquiry;
use App\Models\LeaseContract;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * The admin bell: one notification per piece of waiting work, e.g.
 * "Juan Dela Cruz sent an application · 2h ago".
 *
 * Items are built live from the database, so each one disappears by itself
 * once the work is done (application approved, proof reviewed, ...). The
 * only thing stored is which items each admin has already read
 * (admin_notification_reads), which drives the unread badge and highlight.
 */
class AdminNotificationController extends Controller
{
    /** Most items shown in the panel; the unread badge still counts all. */
    private const LIMIT = 60;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('privileges');
        $me = $user->id;

        // Open emergencies (Critical tickets) that are this admin's job:
        // assigned to them, or assigned to nobody yet. The bell pops these
        // up on screen until someone responds.
        $emergencies = MaintenanceTicket::with('bed.room', 'assignees:id,name', 'tenant')
            ->where('priority', 'critical')
            ->whereNotIn('status', MaintenanceTicket::CLOSED_STATUSES)
            ->whereNull('responded_at')
            ->latest()
            ->get()
            ->filter(fn ($t) => $t->assignees->isEmpty() || $t->assignees->contains('id', $me))
            ->values();
        // Emergency pop-ups open Tickets, so only admins who can manage tenants get them.
        if (! $user->hasPrivilege('manage_tenants')) {
            $emergencies = collect();
        }

        $items = collect($this->items($emergencies))
            // Hide items for sections this admin has no privilege for.
            ->filter(fn ($i) => EnsureAdminHasPrivilege::canOpen($user, $i['link']))
            ->sortByDesc(fn ($i) => $i['time']->getTimestamp())
            ->values();

        $read = DB::table('admin_notification_reads')
            ->where('user_id', $me)
            ->whereIn('item_key', $items->pluck('key'))
            ->pluck('item_key')
            ->flip();

        $items = $items->map(fn ($i) => [
            'key' => $i['key'],
            'kind' => $i['kind'],
            'urgent' => $i['urgent'],
            'actor' => $i['actor'],
            'text' => $i['text'],
            'link' => $i['link'],
            'time' => $i['time']->toIso8601String(),
            'ago' => $i['time']->diffForHumans(null, Carbon::DIFF_RELATIVE_TO_NOW, true),
            'read' => $read->has($i['key']),
        ]);
        $unread = $items->where('read', false)->count();

        return response()->json([
            'total' => $unread,
            'unread' => $unread,
            'items' => $items->take(self::LIMIT)->values(),
            'emergencies' => $emergencies->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'category' => $t->category_label,
                'room' => $t->bed?->room?->room_no,
                'mine' => $t->assignees->isNotEmpty(),
                'team' => $t->assignees->pluck('name')->values(),
                'filed' => $t->created_at->diffForHumans(),
            ]),
        ]);
    }

    /** POST /admin/notifications/read  { keys: [...] } — one item, or "mark all". */
    public function markRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'keys' => ['required', 'array', 'max:500'],
            'keys.*' => ['string', 'max:80'],
        ]);

        $now = now();
        DB::table('admin_notification_reads')->insertOrIgnore(
            collect($data['keys'])->unique()->map(fn ($k) => [
                'user_id' => $request->user()->id, 'item_key' => $k, 'read_at' => $now,
            ])->all()
        );

        // Old read marks are useless once their items are gone; keep the table small.
        DB::table('admin_notification_reads')->where('read_at', '<', now()->subDays(120))->delete();

        return response()->json(['ok' => true]);
    }

    /** Every waiting piece of work, as one notification each. */
    private function items($emergencies): array
    {
        $items = [];
        $add = function (string $key, string $kind, bool $urgent, ?string $actor, string $text, string $link, $time) use (&$items) {
            $items[] = [
                'key' => $key, 'kind' => $kind, 'urgent' => $urgent,
                'actor' => $actor ? trim($actor) : null, 'text' => $text, 'link' => $link,
                'time' => $time ? Carbon::parse($time) : now(),
            ];
        };
        $peso = fn ($n) => '₱'.number_format((float) $n, 2);
        $name = fn ($tenant) => $tenant?->full_name;

        foreach ($emergencies as $t) {
            $add('emergency:'.$t->id, 'emergency', true, null,
                'Emergency: "'.$t->title.'"'.($t->bed?->room ? ' in Room '.$t->bed->room->room_no : '')
                .($t->assignees->isEmpty() ? '. No one is assigned yet.' : ' is assigned to you.'),
                '/tickets?open='.$t->id, $t->created_at);
        }

        $overdueSince = now()->subDays(NotifyOverdueApplications::DAYS);
        foreach (Application::where('status', 'pending')->latest()->get() as $a) {
            $late = $a->created_at <= $overdueSince;
            // A new key once it's late, so it shows up as unread again.
            $add(($late ? 'application_late:' : 'application:').$a->id, 'application', $late, $a->full_name,
                $late ? 'has been waiting '.NotifyOverdueApplications::DAYS.'+ days for a decision on their application.' : 'sent an application.',
                '/applications', $a->created_at);
        }

        foreach (Payment::with('tenant')->where('status', 'pending')->latest()->get() as $p) {
            $add('payment:'.$p->id, 'payment', false, $name($p->tenant),
                'uploaded a payment proof of '.$peso($p->amount_paid).'.', '/payments', $p->created_at);
        }

        foreach (Inquiry::where('status', 'new')->latest()->get() as $q) {
            $add('inquiry:'.$q->id, 'inquiry', false, $q->full_name, 'sent an inquiry.', '/inquiries', $q->created_at);
        }

        $active = MaintenanceTicket::with('tenant')->whereNotIn('status', MaintenanceTicket::CLOSED_STATUSES)->get();
        foreach ($active as $t) {
            $link = '/tickets?open='.$t->id;
            if ($t->status === 'open' && ! $emergencies->contains('id', $t->id)) {
                $add('ticket:'.$t->id, 'ticket', false, $name($t->tenant), 'filed a ticket: "'.$t->title.'".', $link, $t->created_at);
            }
            if ($t->isOverdue()) {
                $add('ticket_overdue:'.$t->id, 'ticket', true, null, 'Ticket "'.$t->title.'" is past its deadline.', $link, $t->currentDeadline()['due'] ?? $t->created_at);
            } elseif ($t->isDueSoon()) {
                $add('ticket_due_soon:'.$t->id, 'ticket', true, null, 'Ticket "'.$t->title.'" is nearing its deadline.', $link, $t->updated_at);
            }
        }

        foreach (BillingStatement::with('tenant')->where('status', 'overdue')->get() as $b) {
            $add('bill_overdue:'.$b->id, 'bill', true, $name($b->tenant),
                'has an overdue bill of '.$peso($b->total_amount).'.', '/delinquency', $b->due_date);
        }

        // Shown for a week after the bed was released (reservations:expire).
        foreach (BillingStatement::with('tenant')->where('reservation_expired_at', '>=', now()->subDays(7))->get() as $b) {
            $add('reservation_expired:'.$b->id, 'reservation', true, $name($b->tenant),
                'did not finish paying the move-in fee. The reservation expired and the bed was released.', '/tenant-manager', $b->reservation_expired_at);
        }

        // Half-paid move-in fees whose one-month reservation ends within 7 days.
        $halfPaid = BillingStatement::with('tenant')->where('type', 'move_in')->where('status', 'partial')
            ->whereNull('reservation_expired_at')->get();
        foreach ($halfPaid as $b) {
            $deadline = $b->reservationDeadline();
            if ($deadline?->lte(now()->addDays(7))) {
                $add('reservation_expiring:'.$b->id, 'reservation', false, $name($b->tenant),
                    'still owes half of the move-in fee. The reservation ends '.$deadline->format('M j').'.', '/payments', $deadline->copy()->subDays(7));
            }
        }

        foreach (Review::with('tenant')->where('status', 'hidden')->whereNull('moderated_by')->get() as $r) {
            $add('review:'.$r->id, 'review', false, $name($r->tenant), 'left a review that the filter held back.', '/dormitory-profile', $r->created_at);
        }

        $leases = LeaseContract::with('tenant')->whereIn('status', ['active', 'expiring_soon'])
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])->get();
        foreach ($leases as $l) {
            $end = Carbon::parse($l->end_date);
            $add('lease_ending:'.$l->id, 'lease', false, $name($l->tenant),
                "'s lease ends on ".$end->format('M j').'.', '/tenant-manager?lease=expiring_soon', $end->copy()->subDays(30));
        }

        return $items;
    }
}
