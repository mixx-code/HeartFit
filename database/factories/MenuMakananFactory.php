<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuMakananFactory extends Factory
{
    public function definition(): array
    {
        $makananSiang = [
            'Nasi Merah',
            'Ayam Suwir Daun Kemangi',
            'Tumis Buncis Putren',
            'Oseng Tempe Cabe Ijo',
            'Sayur Bayam Jagung',
            'Perkedel Kentang',
            'Sambal Matah',
            'Buah Apel',
            'Sup Jagung Telur',
            'Tahu Goreng',
        ];

        $makananMalam = [
            'Grilled Chicken',
            'Potato Wedges (Panggang)',
            'Vegetable Salad',
            'Soup Brokoli Cream',
            'Beef Brown Sauce',
            'Tahu Lada Hitam',
            'Sup Wortel Ayam',
            'Buah Jeruk',
            'Tempe Orek Pedas',
            'Capcay Sayur',
        ];

        // Pilih menu_number (1–11)
        $menuNumber = $this->faker->numberBetween(1, 11);

        // Mapping serve_days sesuai aturan form/controller
        $serveDays = ($menuNumber === 11)
            ? [31]
            : array_values(
                array_filter([$menuNumber, $menuNumber + 10, $menuNumber + 20], fn($d) => $d <= 31)
            );

        // Bangun isi menu acak
        $specMenu = [
            'Makan Siang' => $this->faker->randomElements($makananSiang, rand(3, 4)),
            'Makan Malam' => $this->faker->randomElements($makananMalam, rand(3, 4)),
        ];

        return [
            'nama_menu'  => 'Menu ' . $menuNumber,
            'serve_days' => $serveDays,
            'batch'      => $this->faker->randomElement(['I', 'II', 'III', 'IV']),
            'spec_menu'  => $specMenu,
            'created_by' => 1,
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
