<?php

namespace App\Http\Controllers;

use App\Models\AdminAccessLog;
use App\Models\AdminPrivilege;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Use Case Report — Admin Privileges (DT&TM Week 2, Wed Aug 12 / Thu Aug
 * 13). Every route here sits behind ['auth','admin','privileges'] — the
 * 'privileges' middleware is the Dormitory-Owner-only gate.
 */
class AdminPrivilegeController extends Controller
{
    private const PRIVILEGES = [
        'manage_tenants' => 'Manage Tenants',
        'manage_rooms' => 'Manage Rooms',
        'manage_contracts' => 'Manage Contracts',
        'manage_billing' => 'Manage Billing',
        'manage_users' => 'Manage Admin Users',
        'view_reports' => 'View Reports',
    ];

    public function index(Request $request)
    {
        $admins = User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
            ->with('privileges')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $lastLog = AdminAccessLog::where('user_id', $user->id)->latest('created_at')->first();
                $grantedAt = $lastLog?->created_at ?? $user->privileges->min('granted_at');

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => (bool) $user->is_active,
                    'privileges' => $user->privileges->pluck('privilege_name')->values(),
                    'granted_at' => $grantedAt ? Carbon::parse($grantedAt)->toIso8601String() : null,
                ];
            })
            ->values();

        return view('adminprivileges', [
            'admins' => $admins,
            'privilegeOptions' => self::PRIVILEGES,
            'activeAdminsCount' => $admins->where('is_active', true)->count(),
            'totalGrants' => AdminPrivilege::count(),
            'currentUserId' => $request->user()->id,
        ]);
    }

    /**
     * "Add Admin Account" — creates a brand new admin login (not tied to
     * a tenant record) with whichever starting privileges the Owner picks.
     * Returns a temporary password since there's no guaranteed working
     * email delivery to hand it off otherwise.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'privileges' => ['array'],
            'privileges.*' => [Rule::in(array_keys(self::PRIVILEGES))],
        ]);

        $adminRole = Role::where('role_name', 'admin')->firstOrFail();
        $temporaryPassword = Str::random(10);

        $user = DB::transaction(function () use ($data, $adminRole, $temporaryPassword, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);

            foreach (($data['privileges'] ?? []) as $privilege) {
                AdminPrivilege::create([
                    'user_id' => $user->id,
                    'granted_by' => $request->user()?->id,
                    'privilege_name' => $privilege,
                ]);
            }

            AdminAccessLog::create([
                'user_id' => $user->id,
                'performed_by' => $request->user()?->id,
                'action' => 'granted',
                'note' => 'Admin account created.',
            ]);

            return $user;
        });

        return response()->json([
            'message' => $user->name.'\'s admin account has been created.',
            'temporary_password' => $temporaryPassword,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ], 201);
    }

    /**
     * "Manage Privileges" modal save. Syncs the checked list — adds what's
     * missing, removes what's unchecked.
     */
    public function updatePrivileges(Request $request, User $user): JsonResponse
    {
        $this->ensureIsAdmin($user);

        $data = $request->validate([
            'privileges' => ['array'],
            'privileges.*' => [Rule::in(array_keys(self::PRIVILEGES))],
        ]);

        $selected = $data['privileges'] ?? [];

        // Safety net beyond the literal use case: stops the Owner from
        // accidentally locking themselves out of this page.
        if ($user->id === $request->user()?->id && ! in_array('manage_users', $selected, true)) {
            return response()->json([
                'message' => 'You cannot remove your own Manage Admin Users privilege.',
            ], 422);
        }

        DB::transaction(function () use ($user, $selected, $request) {
            AdminPrivilege::where('user_id', $user->id)
                ->whereNotIn('privilege_name', $selected)
                ->delete();

            foreach ($selected as $privilege) {
                AdminPrivilege::firstOrCreate(
                    ['user_id' => $user->id, 'privilege_name' => $privilege],
                    ['granted_by' => $request->user()?->id]
                );
            }

            AdminAccessLog::create([
                'user_id' => $user->id,
                'performed_by' => $request->user()?->id,
                'action' => 'privileges_updated',
                'note' => 'Privileges updated: '.(empty($selected) ? 'none' : implode(', ', $selected)),
            ]);
        });

        return response()->json([
            'message' => $user->name.'\'s privileges have been updated.',
            'privileges' => $user->fresh()->privileges->pluck('privilege_name'),
        ]);
    }

    /**
     * Demotes the account back to tenant role and deactivates the login,
     * matching the same is_active convention used by Deactivate Tenant
     * Account (Table 38). Also strips every admin_privileges row.
     */
    public function revoke(Request $request, User $user): JsonResponse
    {
        $this->ensureIsAdmin($user);

        if ($user->id === $request->user()?->id) {
            return response()->json(['message' => 'You cannot revoke your own admin access.'], 422);
        }

        $tenantRole = Role::where('role_name', 'tenant')->firstOrFail();

        DB::transaction(function () use ($user, $tenantRole, $request) {
            AdminPrivilege::where('user_id', $user->id)->delete();

            $user->update([
                'role_id' => $tenantRole->id,
                'is_active' => false,
            ]);

            AdminAccessLog::create([
                'user_id' => $user->id,
                'performed_by' => $request->user()?->id,
                'action' => 'revoked',
                'note' => 'Admin access revoked.',
            ]);
        });

        return response()->json([
            'message' => $user->name.'\'s admin access has been revoked.',
        ]);
    }

    private function ensureIsAdmin(User $user): void
    {
        abort_unless($user->role?->role_name === 'admin', 404);
    }
}