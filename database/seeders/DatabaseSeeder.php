<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@energea.me',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $user = User::where('email', 'admin@energea.me')->first();

        if (! $user) {
            $this->command->warn('User admin@energea.me not found.');

            return;
        }

        $user->update([
            'password' => Hash::make('password'),
        ]);
    }
}
