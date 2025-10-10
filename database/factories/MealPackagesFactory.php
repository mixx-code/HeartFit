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
            ?? PackageType::factory()->create(['packageType' => 'Reguler'])->id;

        $batchRoman = $this->toRoman($this->faker->numberBetween(1, 10));

        // daftar porsi yang kamu minta
        $porsiOptions = [
            '4 hari 2 kali makan (siang dan sore)',
            '8 hari 1 kali makan (siang/malam saja)',
            '12 hari 2 kali makan (siang dan sore)',
            '24 hari 1 kali makan (siang/malam saja)',
            '36 hari 2 kali makan (siang dan sore)',
            '72 hari 1 kali makan (siang/malam saja)',
            '2 kali makan (siang dan malam)',
            'harga per porsi'
        ];

        return [
            'nama_meal_package' => $this->faker->randomElement([
                'Healthy Fit Plan',
                'Diet Booster',
                'Protein Pack',
                'Vegan Delight',
                'Slim & Fresh'
            ]),
            'batch'           => $batchRoman,
            'jenis_paket'     => $this->faker->randomElement(['harian', 'paket mingguan', 'paket bulanan', 'paket 3 bulanan']),
            'porsi_paket'     => $this->faker->randomElement($porsiOptions),
            'total_hari'      => $this->faker->randomElement([1, 4, 8, 12, 24, 36, 72]),
            'detail_paket'    => 'makan (Siang dan Sore)',
            'price'           => $this->faker->randomFloat(2, 50000, 500000), // harga antara 50rb–500rb
            'package_type_id' => $typeId,
        ];
    }
}
