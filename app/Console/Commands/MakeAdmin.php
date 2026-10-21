<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('energea:make-admin {email? : Email address of the admin user} {--name= : Name used when creating a new user} {--password= : Password used when creating a new user}')]
#[Description('Promote an existing user to admin, or create a new admin user')]
class MakeAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email address');

        if (! is_string($email) || $email === '') {
            $this->error('An email address is required.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user !== null) {
            $user->forceFill(['is_admin' => true])->save();

            $this->info("{$user->email} is now an admin.");

            return self::SUCCESS;
        }

        $name = $this->option('name') ?: Str::headline(Str::before($email, '@'));
        $password = $this->option('password') ?: Str::password(16);

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
        $user->forceFill(['is_admin' => true])->save();

        $this->info('Admin user created.');
        $this->table(['Email', 'Password'], [[$user->email, $password]]);

        return self::SUCCESS;
    }
}
