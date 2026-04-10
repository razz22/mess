<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeSuperAdmin extends Command
{
    protected $signature   = 'mess:make-super-admin {email? : Email of the user to promote}';
    protected $description = 'Promote an existing user to super admin, or create a new super admin account';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Enter the email of the user to promote (or new email to create one)');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->warn("No user found with email: {$email}");

            if (!$this->confirm('Create a new super admin account with this email?', true)) {
                $this->info('Aborted.');
                return self::FAILURE;
            }

            $name     = $this->ask('Enter name');
            $password = $this->secret('Enter password (min 6 chars)');

            $user = User::create([
                'name'           => $name,
                'email'          => $email,
                'password'       => Hash::make($password),
                'is_active'      => true,
                'is_super_admin' => true,
            ]);

            $this->info("✓ Super admin account created: {$user->name} ({$user->email})");
            return self::SUCCESS;
        }

        if ($user->is_super_admin) {
            $this->warn("{$user->name} is already a super admin.");
            return self::SUCCESS;
        }

        $user->update(['is_super_admin' => true]);
        $this->info("✓ {$user->name} ({$user->email}) has been promoted to Super Admin.");

        return self::SUCCESS;
    }
}
