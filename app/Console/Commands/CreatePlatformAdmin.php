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
 *
 * Hosts whose command box can't answer questions (e.g. Laravel Cloud) can
 * pass everything at once:
 *
 *     php artisan nestph:create-platform-admin --name="NEST.PH Team" --email=team@example.com --password="..."
 */
class CreatePlatformAdmin extends Command
{
    protected $signature = 'nestph:create-platform-admin
        {--name= : Full name (skips the question)}
        {--email= : Login email (skips the question)}
        {--password= : Password (skips the question)}';

    protected $description = 'Create a NEST.PH platform team account (reviews dormitory owner requests).';

    public function handle(): int
    {
        $this->info('NEST.PH - create a platform team account');
        $this->line('This account can log in at /login/platform. It cannot open any dormitory\'s admin pages.');
        $this->newLine();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ];

        // Non-interactive: all three given as options.
        if ($this->option('name') && $this->option('email') && $this->option('password')) {
            $input = [
                'name' => trim($this->option('name')),
                'email' => trim($this->option('email')),
                'password' => $this->option('password'),
            ];
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $this->error($message);
                }

                return self::FAILURE;
            }

            return $this->createAccount($input['name'], $input['email'], $input['password']);
        }

        $name = $this->askValid('Full name', 'name', $rules['name']);
        $email = $this->askValid('Email (used to log in)', 'email', $rules['email']);

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

        return $this->createAccount($name, $email, $password);
    }

    private function createAccount(string $name, string $email, string $password): int
    {
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
