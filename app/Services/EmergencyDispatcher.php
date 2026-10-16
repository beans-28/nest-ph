<?php

namespace App\Services;

use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Who is online right now, and who gets sent to an emergency.
 *
 * "Online" = an active admin whose session (the `sessions` table) did
 * something in the last ONLINE_MINUTES. Every admin page checks the bell
 * every 30 seconds, so an admin with any admin page open stays online.
 *
 * Emergencies (priority "critical") are given to TEAM_SIZE online admins
 * at once, picking whoever has the fewest emergencies open already. So if
 * two emergencies come in together, they are spread across the staff
 * instead of all landing on one person.
 */
class EmergencyDispatcher
{
    public const ONLINE_MINUTES = 5;

    public const TEAM_SIZE = 2;

    /** @return Collection<int, int> ids of active admins who are online */
    public static function onlineAdminIds(): Collection
    {
        // Presence comes from the database session store; with any other
        // session driver nobody shows as online (and nothing auto-assigns).
        if (config('session.driver') !== 'database') {
            return collect();
        }

        $recentUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(self::ONLINE_MINUTES)->timestamp)
            ->distinct()
            ->pluck('user_id');

        return User::whereIn('id', $recentUserIds)
            ->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
            ->pluck('id');
    }

    /** Open emergencies each admin is on right now: [user_id => count]. */
    public static function openEmergencyLoad(): Collection
    {
        return DB::table('maintenance_ticket_assignees as a')
            ->join('maintenance_tickets as t', 't.id', '=', 'a.ticket_id')
            ->where('t.priority', 'critical')
            ->whereNotIn('t.status', MaintenanceTicket::CLOSED_STATUSES)
            ->groupBy('a.user_id')
            ->pluck(DB::raw('COUNT(*)'), 'a.user_id');
    }

    /**
     * Assigns online admins to a new critical ticket. Returns how many were
     * assigned (0 when nobody is online -- the bell then flags it as
     * "no one assigned" for every admin).
     */
    public static function autoAssign(MaintenanceTicket $ticket): int
    {
        if ($ticket->priority !== 'critical') {
            return 0;
        }

        $load = self::openEmergencyLoad();
        $team = self::onlineAdminIds()
            ->sortBy(fn ($id) => [(int) ($load[$id] ?? 0), $id])
            ->take(self::TEAM_SIZE)
            ->values();

        if ($team->isEmpty()) {
            return 0;
        }

        $ticket->assignees()->syncWithoutDetaching(
            $team->mapWithKeys(fn ($id) => [$id => ['auto_assigned' => true]])->all()
        );
        $ticket->update(['assigned_to' => $ticket->assigned_to ?? $team->first()]);

        return $team->count();
    }
}
