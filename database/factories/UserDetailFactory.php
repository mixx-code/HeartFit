<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bb = $this->faker->numberBetween(45, 95);
        $tb = $this->faker->numberBetween(150, 195);

        return [
            'user_id' => User::factory()->create([
                'role' => 'customer',
                'password' => bcrypt('password123!'),
            ])->id,
            'mr'            => 'MR-' . $this->faker->unique()->numerify('########'),
            'nik'           => $this->faker->unique()->numerify('################'),
            'alamat'        => $this->faker->address(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir'  => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-18 years'),
            'bb_tb'         => "{$bb}/{$tb}",
            'foto_ktp_base64' => null,
            'hp'            => $this->faker->e164PhoneNumber(),
            'usia'          => $this->faker->numberBetween(18, 65),
            'created_by'    => null,
            'updated_by'    => null,
        ];
    }
}
