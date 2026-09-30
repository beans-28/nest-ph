<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantNotification;
use App\Services\TenantNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The tenant's notification panel (bell icon, partials/tenant-notifications).
 */
class TenantNotificationController extends Controller
{
    public function __construct(private TenantNotificationService $service)
    {
    }

    private function tenant(Request $request): Tenant
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()?->tenant;

        if (! $tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        return $tenant;
    }

    /** GET /my/notifications — newest 50, plus the unread count. */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);
        $this->service->syncAutomatic($tenant);

        $items = TenantNotification::where('tenant_id', $tenant->id)
            ->latest('id')
            ->take(50)
            ->get();

        return response()->json([
            'unread' => TenantNotification::where('tenant_id', $tenant->id)->whereNull('read_at')->count(),
            'notifications' => $items->map->toClientArray()->values(),
        ]);
    }

    /** PATCH /my/notifications/{notification}/read */
    public function markRead(Request $request, TenantNotification $notification): JsonResponse
    {
        if ($notification->tenant_id !== $this->tenant($request)->id) {
            throw new NotFoundHttpException('Notification not found.');
        }

        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return response()->json(['ok' => true]);
    }

    /** POST /my/notifications/read-all */
    public function markAllRead(Request $request): JsonResponse
    {
        TenantNotification::where('tenant_id', $this->tenant($request)->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
