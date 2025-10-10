<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuMakanan;

class MenuMakananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Loop batch (contoh: Batch I & Batch II)
        foreach (['I', 'II'] as $batchRomawi) {
            // Setiap batch punya menu 1–11
            foreach (range(1, 11) as $menuNumber) {
                // Tentukan serve_days sesuai aturan JS/controller
                $serveDays = ($menuNumber === 11)
                    ? [31]
                    : array_values(
                        array_filter([$menuNumber, $menuNumber + 10, $menuNumber + 20], fn($d) => $d <= 31)
                    );

                MenuMakanan::factory()->create([
                    'batch'      => $batchRomawi,
                    'nama_menu'  => 'Menu ' . $menuNumber,
                    'serve_days' => $serveDays,
                ]);
            }
        }
    }
}
