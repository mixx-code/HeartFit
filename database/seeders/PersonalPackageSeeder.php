<?php

namespace Database\Seeders;

use App\Models\MealPackages;
use App\Models\PackageType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Personal package type
        $personalType = PackageType::where('packageType', 'Personal')->first();

        if ($personalType) {
            // Check if Personal package already exists
            $existingPackage = MealPackages::where('nama_meal_package', 'PERSONAL NUTRITION PLAN')
                ->where('package_type_id', $personalType->id)
                ->first();

            if (!$existingPackage) {
                // Create Personal package with price 700,000
                MealPackages::create([
                    'nama_meal_package' => 'PERSONAL NUTRITION PLAN',
                    'price' => 700000,
                    'total_hari' => 30,
                    'porsi_paket' => '30 Hari',
                    'detail_paket' => '3 Kali Makan (Pagi, Siang & Malam) + Konsultasi Ahli Gizi',
                    'jenis_paket' => 'paket bulanan',
                    'package_type_id' => $personalType->id,
                    'batch' => 'I',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->command->info('Personal package created successfully!');
            } else {
                $this->command->info('Personal package already exists!');
            }
        } else {
            $this->command->error('Personal package type not found!');
        }
    }
}
