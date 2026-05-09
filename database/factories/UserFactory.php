<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => 'customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // default password
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => [
            'role' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => [
            'role' => 'superadmin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
