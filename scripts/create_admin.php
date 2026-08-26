<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

$email = 'ryzadl@admin.com';
$password = 'nestph2005';
$name = 'Ryzad L';

$role = Role::firstOrCreate(['role_name' => 'admin']);

$user = User::where('email', $email)->first();
if ($user) {
    echo "User already exists: {$user->email}\n";
    $user->role_id = $role->id;
    $user->role = $role;
    $user->is_active = 1;
    $user->save();
    echo "Assigned admin role to existing user.\n";
    exit(0);
}

$user = new User();
$user->name = $name;
$user->email = $email;
$user->password = Hash::make($password);
$user->role_id = $role->id;
$user->is_active = 1;
$user->save();

echo "Created admin user: {$user->email}\n";
