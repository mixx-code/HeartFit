<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealPackages;
use App\Models\PackageType;

class MealPackagesSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada 2 tipe paket (Reguler dan Premium)
        $reguler = PackageType::firstOrCreate(
            ['id' => 1],
            ['packageType' => 'Reguler']
        );

        $premium = PackageType::firstOrCreate(
            ['id' => 2],
            ['packageType' => 'Premium']
        );

        // 6 reguler utk type 1
        MealPackages::factory()->mingguanDuaKaliReguler()->create(['package_type_id' => 1]);
        MealPackages::factory()->mingguanSatuKaliReguler()->create(['package_type_id' => 1]);
        MealPackages::factory()->bulananDuaKaliReguler()->create(['package_type_id' => 1]);
        MealPackages::factory()->bulananSatuKaliReguler()->create(['package_type_id' => 1]);
        MealPackages::factory()->tigaBulananDuaKaliReguler()->create(['package_type_id' => 1]);
        MealPackages::factory()->tigaBulananSatuKaliReguler()->create(['package_type_id' => 1]);

        // 6 premium utk type 2
        MealPackages::factory()->mingguanDuaKali()->create(['package_type_id' => 2]);
        MealPackages::factory()->mingguanSatuKali()->create(['package_type_id' => 2]);
        MealPackages::factory()->bulananDuaKali()->create(['package_type_id' => 2]);
        MealPackages::factory()->bulananSatuKali()->create(['package_type_id' => 2]);
        MealPackages::factory()->tigaBulananDuaKali()->create(['package_type_id' => 2]);
        MealPackages::factory()->tigaBulananSatuKali()->create(['package_type_id' => 2]);

        // ============================================
        // TAMBAHAN: Paket Harian
        // ============================================

        // Harian Reguler
        MealPackages::factory()->harianReguler()->create(['package_type_id' => 1]);

        // Harian Premium
        MealPackages::factory()->harianPremium()->create(['package_type_id' => 2]);
    }
}