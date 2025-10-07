<?php

namespace Database\Seeders;

use App\Models\packageType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('package_types')->insert([
            ['packageType' => 'Reguler',   'created_at' => $now, 'updated_at' => $now],
            ['packageType' => 'Premium',   'created_at' => $now, 'updated_at' => $now],
            ['packageType' => 'Exclusive', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
