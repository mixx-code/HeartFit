<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'superadmin@mail.com'],
            ['name' => 'superadmin', 'password' => Hash::make('superadmin123'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'customer@mail.com'],
            ['name' => 'customer', 'password' => Hash::make('customer123'), 'role' => 'customer']
        );
    }
}
