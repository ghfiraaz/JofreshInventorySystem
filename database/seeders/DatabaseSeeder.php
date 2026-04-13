<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Creates the initial Owner (Superadmin) account.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'owner@jofresh.com'],
            [
                'name'     => 'Owner JoFresh',
                'password' => Hash::make('password123'),
                'role'     => 'Superadmin',
            ]
        );
    }
}
