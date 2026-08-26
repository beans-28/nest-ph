<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

class UserManagementController extends Controller
{
    public function grantAdmin(User $user)
    {
        $adminRole = Role::where('role_name', 'admin')->firstOrFail();

        $user->update(['role_id' => $adminRole->id]);

        return response()->json([
            'message' => "{$user->name} is now an admin.",
            'user' => $user->only(['id', 'name', 'email', 'role_id']),
        ]);
    }

    public function revokeAdmin(User $user)
    {
        $tenantRole = Role::where('role_name', 'tenant')->firstOrFail();

        $user->update(['role_id' => $tenantRole->id]);

        return response()->json([
            'message' => "{$user->name}'s admin privileges have been revoked.",
            'user' => $user->only(['id', 'name', 'email', 'role_id']),
        ]);
    }
}
