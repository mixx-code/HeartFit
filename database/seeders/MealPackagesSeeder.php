<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealPackages;
use App\Models\PackageType;

class MealPackagesSeeder extends Seeder
{
    public function run(): void
    {
        // Buat dulu jenis package type
        $types = [
            'Reguler',
            'Premium',
            'Exclusive',
        ];

        foreach ($types as $name) {
            $type = PackageType::firstOrCreate(['packageType' => $name]);

            // Buat 5 paket makanan untuk setiap tipe
            MealPackages::factory()
                ->count(5)
                ->create([
                    'package_type_id' => $type->id,
                ]);
        }
    }
}
