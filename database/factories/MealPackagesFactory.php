<?php

namespace Database\Factories;

use App\Models\MealPackages;
use App\Models\PackageType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealPackagesFactory extends Factory
{
    protected $model = MealPackages::class;

    private function toRoman($num)
    {
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $returnValue = '';
        while ($num > 0) {
            foreach ($map as $roman => $int) {
                if ($num >= $int) {
                    $num -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }

    public function definition(): array
    {
        $typeId = PackageType::inRandomOrder()->value('id')
            ?? PackageType::factory()->create(['packageType' => 'Reguler'])->id; // fallback kalau kosong

        // Generate angka acak 1–10 lalu ubah ke romawi
        $batchRoman = $this->toRoman($this->faker->numberBetween(1, 10));

        return [
            'batch'           => $batchRoman,
            'jenis_paket'     => $this->faker->randomElement(['harian', 'mingguan', 'bulanan']),
            'porsi_paket'     => $this->faker->randomElement(['4 Hari', '8 Hari', '12 Hari']),
            'detail_paket'    => '2 Kali Makan (Siang dan Sore)',
            'package_type_id' => $typeId,
        ];
    }
}
