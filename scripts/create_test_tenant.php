<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;

$role = Role::firstOrCreate(['role_name' => 'tenant']);

$password = bcrypt('tenantpass');

$user = User::firstOrCreate(
    ['email' => 'test.tenant@local'],
    [
        'name' => 'Test Tenant',
        'password' => $password,
        'role_id' => $role->id,
        'is_active' => 1,
    ]
);

$tenant = Tenant::firstOrCreate(
    ['email' => $user->email],
    [
        'user_id' => $user->id,
        'full_name' => $user->name,
        'contact_number' => '09991234567'
    ]
);

echo "Created/Found user id={$user->id}, email={$user->email}\n";
echo "Tenant id={$tenant->id}\n";
