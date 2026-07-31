<?php

namespace Database\Seeders;

use App\Models\AdminPrivilege;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $tenantRole = Role::firstOrCreate(['role_name' => 'tenant']);
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);

        // 2. Test accounts (password for all: "password123")
        $tenant = User::updateOrCreate(
            ['email' => 'tenant@nestph.test'],
            [
                'name' => 'Test Tenant',
                'password' => Hash::make('password123'),
                'role_id' => $tenantRole->id,
                'is_active' => true,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@nestph.test'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        $owner = User::updateOrCreate(
            ['email' => 'owner@nestph.test'],
            [
                'name' => 'Test Owner',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        // 3. Owner gets ALL admin privileges
        $allPrivileges = [
            'manage_tenants',
            'manage_rooms',
            'manage_contracts',
            'manage_billing',
            'manage_users',
            'view_reports',
        ];

        foreach ($allPrivileges as $privilege) {
            AdminPrivilege::firstOrCreate([
                'user_id' => $owner->id,
                'privilege_name' => $privilege,
            ]);
        }

        // 4. Regular admin gets a limited subset (no manage_users)
        $limitedPrivileges = [
            'manage_tenants',
            'manage_rooms',
            'manage_billing',
            'view_reports',
        ];

        foreach ($limitedPrivileges as $privilege) {
            AdminPrivilege::firstOrCreate([
                'user_id' => $admin->id,
                'privilege_name' => $privilege,
            ]);
        }
    }
}
