<?php

namespace App\Console\Commands;

use App\Models\AdminPrivilege;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * First-time setup for a new dormitory. Only an Owner can create admin
 * accounts from the Admin Privileges page, so a fresh install has no way
 * to create the very first one from the website -- this command does it
 * from the server's terminal instead:
 *
 *     php artisan nestph:create-owner
 */
class CreateOwner extends Command
{
    protected $signature = 'nestph:create-owner';

    protected $description = 'Create the Dormitory Owner admin account (all privileges) for a new NEST.PH installation.';

    /**
     * Same keys as AdminPrivilegeController::PRIVILEGES. manage_users is
     * what makes someone the Owner (see EnsureCanManagePrivileges).
     */
    private const ALL_PRIVILEGES = [
        'manage_tenants',
        'manage_rooms',
        'manage_contracts',
        'manage_billing',
        'manage_users',
        'view_reports',
    ];

    public function handle(): int
    {
        $this->info('NEST.PH - create the Dormitory Owner account');
        $this->line('This account can log in at /login/admin and create every other admin account.');
        $this->newLine();

        $existingOwners = User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))
            ->whereHas('privileges', fn ($q) => $q->where('privilege_name', 'manage_users'))
            ->pluck('email');

        if ($existingOwners->isNotEmpty()) {
            $this->warn('An Owner account already exists: ' . $existingOwners->join(', '));
            $this->line('They can add more admins from the Admin Privileges page instead.');

            if (! $this->confirm('Create another Owner account anyway?', false)) {
                $this->line('Nothing was created.');

                return self::SUCCESS;
            }
        }

        $name = $this->askValid('Owner\'s full name', 'name', ['required', 'string', 'max:255']);
        $email = $this->askValid('Owner\'s email (used to log in)', 'email', ['required', 'email', 'max:255', 'unique:users,email']);

        while (true) {
            $password = (string) $this->secret('Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)');

            $check = \Illuminate\Support\Facades\Validator::make(
                ['password' => $password],
                ['password' => [\Illuminate\Validation\Rules\Password::defaults()]]
            );
            if ($check->fails()) {
                foreach ($check->errors()->get('password') as $message) {
                    $this->error($message);
                }
                continue;
            }

            if ($password !== (string) $this->secret('Type the password again')) {
                $this->error('The two passwords did not match. Please try again.');
                continue;
            }

            break;
        }

        DB::transaction(function () use ($name, $email, $password) {
            // Roles normally come from the seeder; create them here too so
            // this works on a completely empty database.
            Role::firstOrCreate(['role_name' => 'tenant']);
            $adminRole = Role::firstOrCreate(['role_name' => 'admin']);

            $owner = User::forceCreate([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);

            foreach (self::ALL_PRIVILEGES as $privilege) {
                AdminPrivilege::create([
                    'user_id' => $owner->id,
                    'privilege_name' => $privilege,
                ]);
            }
        });

        $this->newLine();
        $this->info("Owner account created for {$name} ({$email}).");
        $this->line('Log in at ' . rtrim(config('app.url'), '/') . '/login/admin');

        return self::SUCCESS;
    }

    /** Keep asking until the answer passes the given validation rules. */
    private function askValid(string $question, string $field, array $rules): string
    {
        while (true) {
            $answer = trim((string) $this->ask($question));
            $validator = Validator::make([$field => $answer], [$field => $rules]);

            if ($validator->passes()) {
                return $answer;
            }

            $this->error($validator->errors()->first($field));
        }
    }
}
