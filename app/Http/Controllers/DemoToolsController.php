<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotifyOverdueApplications;
use App\Models\Application;
use App\Models\BillingStatement;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Testing/demo-only "Testing Tools" panels (partials/demo-tools). Not a
 * manuscript use case. Several features only change with time (overdue
 * tickets, applications waiting 3+ days, expired reservations) or with the
 * daily scheduler, which is off on Laravel Cloud. These endpoints move one
 * record's clock back and run the same scheduled command the server would,
 * so a demo shows the real behavior without waiting days.
 *
 * Hidden and blocked unless config('app.demo_tools') is on, same switch as
 * the Delinquency testing tools.
 */
class DemoToolsController extends Controller
{
    public function items(string $tool): JsonResponse
    {
        $this->guard();

        $items = match ($tool) {
            'reservations' => BillingStatement::with('tenant')
                ->where('type', 'move_in')->where('status', 'partial')->whereNull('reservation_expired_at')
                ->latest()->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'label' => ($b->tenant?->full_name ?? 'Tenant #' . $b->tenant_id)
                        . ': ₱' . number_format($b->remainingBalance(), 2) . ' left, pay by '
                        . ($b->reservationDeadline()?->format('M j, Y') ?? '—'),
                ]),
            'applications' => Application::where('status', 'pending')->latest()->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'label' => $a->full_name . ': submitted ' . $a->created_at->format('M j, Y'),
                ]),
            'tickets' => MaintenanceTicket::whereNotIn('status', MaintenanceTicket::CLOSED_STATUSES)->latest()->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'label' => '#' . $t->id . ' ' . ($t->title ?? $t->category ?? 'Ticket') . ' · '
                        . ($t->isOverdue() ? 'overdue' : ($t->isDueSoon() ? 'due soon' : 'on time')),
                ]),
            'escalation' => collect(),
            default => abort(404),
        };

        return response()->json(['items' => $items->values()]);
    }

    public function run(Request $request, string $tool, string $action): JsonResponse
    {
        $this->guard();
        $id = $request->integer('id');

        $message = match ("$tool.$action") {
            'reservations.expire' => $this->expireReservation($id),
            'reservations.sweep' => $this->command('reservations:expire'),
            'applications.age' => $this->ageApplication($id),
            'applications.sweep' => $this->command('applications:notify-overdue'),
            'tickets.overdue' => $this->moveTicketDeadline($id, overdue: true),
            'tickets.due_soon' => $this->moveTicketDeadline($id, overdue: false),
            'escalation.sweep' => $this->command('escalation:process'),
            default => abort(404),
        };

        return response()->json(['message' => $message]);
    }

    private function guard(): void
    {
        if (! config('app.demo_tools')) {
            abort(404);
        }
    }

    private function command(string $name): string
    {
        Artisan::call($name);

        return trim(Artisan::output()) ?: 'Done.';
    }

    /** Back-date the first half so its one-month reservation has passed, then run the daily expiry. */
    private function expireReservation(int $id): string
    {
        $bill = BillingStatement::where('type', 'move_in')->where('status', 'partial')->findOrFail($id);

        Payment::where('billing_id', $bill->id)->where('status', 'approved')
            ->update(['payment_date' => now()->subMonthNoOverflow()->subDays(2)->toDateString()]);

        return $this->command('reservations:expire');
    }

    /** Make a pending application older than the overdue threshold and let the email go out again. */
    private function ageApplication(int $id): string
    {
        $application = Application::where('status', 'pending')->findOrFail($id);
        $application->timestamps = false;
        $application->forceFill([
            'created_at' => now()->subDays(NotifyOverdueApplications::DAYS + 1),
            'overdue_notified_at' => null,
        ])->save();

        return $application->full_name . ' is now ' . (NotifyOverdueApplications::DAYS + 1)
            . ' days old. ' . $this->command('applications:notify-overdue');
    }

    /** Shift a ticket's clock so its current deadline is just past, or inside the "due soon" window. */
    private function moveTicketDeadline(int $id, bool $overdue): string
    {
        $ticket = MaintenanceTicket::findOrFail($id);
        $deadline = $ticket->currentDeadline();

        if (! $deadline) {
            abort(422, 'Closed tickets have no deadline.');
        }

        $target = $overdue
            ? now()->subMinutes(5)
            : now()->addMinutes(max(1, (int) floor($deadline['window_minutes'] * MaintenanceTicket::DUE_SOON_SHARE / 2)));
        $shift = (int) $target->diffInMinutes($deadline['due'], false); // minutes to move back

        $ticket->timestamps = false;
        $ticket->forceFill(array_filter([
            'created_at' => Carbon::parse($ticket->created_at)->subMinutes($shift),
            'responded_at' => $ticket->responded_at ? Carbon::parse($ticket->responded_at)->subMinutes($shift) : null,
            'revised_due_at' => $ticket->revised_due_at ? Carbon::parse($ticket->revised_due_at)->subMinutes($shift) : null,
        ], fn ($v) => $v !== null))->save();

        return 'Ticket #' . $ticket->id . ' is now ' . ($overdue ? 'overdue.' : 'due soon.');
    }
}
