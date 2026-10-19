<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Creates a NEST.PH team account (platform_admin role). These accounts log
 * in at /login/platform and review dormitory owner requests. Run on the
 * server's terminal:
 *
 *     php artisan nestph:create-platform-admin
 */
class CreatePlatformAdmin extends Command
{
    protected $signature = 'nestph:create-platform-admin';

    protected $description = 'Create a NEST.PH platform team account (reviews dormitory owner requests).';

    public function handle(): int
    {
        $this->info('NEST.PH - create a platform team account');
        $this->line('This account can log in at /login/platform. It cannot open any dormitory\'s admin pages.');
        $this->newLine();

        $name = $this->askValid('Full name', 'name', ['required', 'string', 'max:255']);
        $email = $this->askValid('Email (used to log in)', 'email', ['required', 'email', 'max:255', 'unique:users,email']);

        while (true) {
            $password = (string) $this->secret('Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)');

            $check = Validator::make(['password' => $password], ['password' => [Password::defaults()]]);
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

        $role = Role::firstOrCreate(['role_name' => 'platform_admin']);

        User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->newLine();
        $this->info("Platform account created for {$name} ({$email}).");
        $this->line('Log in at ' . rtrim(config('app.url'), '/') . '/login/platform');

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
