<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserManagementController extends Controller
{
    public function grantAdmin(User $user)
    {
        $user->update(['role' => 'admin']);

        return response()->json([
            'message' => "{$user->name} is now an admin.",
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }

    public function revokeAdmin(User $user)
    {
        $user->update(['role' => 'tenant']);

        return response()->json([
            'message' => "{$user->name}'s admin privileges have been revoked.",
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }
}