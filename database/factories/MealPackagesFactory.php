<?php

namespace Database\Factories;

use App\Models\MealPackages;
use App\Models\PackageType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealPackagesFactory extends Factory
{
    protected $model = MealPackages::class;

    public function definition(): array
    {
        // Pastikan ada PackageType; default ke "Reguler" jika belum ada
        $typeId = PackageType::inRandomOrder()->value('id')
            ?? PackageType::factory()->create(['packageType' => 'Reguler'])->id;

        return [
            'nama_meal_package' => $this->faker->randomElement([
                'Healthy Fit Plan',
                'Diet Booster',
                'Protein Pack',
                'Vegan Delight',
                'Slim & Fresh',
            ]),
            'batch'           => 'I', // fixed
            'jenis_paket'     => 'paket mingguan', // default, akan ditimpa oleh state
            'porsi_paket'     => '4 hari 2 kali makan (siang dan sore)', // default state mingguan #1
            'total_hari'      => 4,
            'detail_paket'    => 'makan (Siang dan Sore)',
            'price'           => $this->faker->randomFloat(2, 50000, 500000),
            'package_type_id' => $typeId,
        ];
    }

    /* =========================
       STATE KHUSUS SESUAI ATURAN
       ========================= */

    // MINGGUAN (2 item)
    public function mingguanDuaKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket mingguan',
            'porsi_paket' => '4 hari 2 kali makan (siang dan sore)',
            'total_hari'  => 4,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price'=> 650000
        ]);
    }

    public function mingguanSatuKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket mingguan',
            'porsi_paket' => '8 hari 1 kali makan (siang/malam saja)',
            'total_hari'  => 8,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price' => 650000
        ]);
    }

    // BULANAN (2 item)
    public function bulananDuaKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket bulanan',
            'porsi_paket' => '12 hari 2 kali makan (siang dan sore)',
            'total_hari'  => 12,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price' => 1950000
        ]);
    }

    public function bulananSatuKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket bulanan',
            'porsi_paket' => '24 hari 1 kali makan (siang/malam saja)',
            'total_hari'  => 24,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price' => 1950000
        ]);
    }

    // 3 BULANAN (2 item)
    public function tigaBulananDuaKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket 3 bulanan',
            'porsi_paket' => '36 hari 2 kali makan (siang dan sore)',
            'total_hari'  => 36,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price' => 5830000
        ]);
    }

    public function tigaBulananSatuKali()
    {
        return $this->state(fn() => [
            'jenis_paket' => 'paket 3 bulanan',
            'porsi_paket' => '72 hari 1 kali makan (siang/malam saja)',
            'total_hari'  => 72,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price' => 5830000
        ]);
    }

    // =========================
    // REGULER (mirror dari premium, harga lebih rendah)
    // =========================

    // MINGGUAN (2 item)
    public function mingguanDuaKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket mingguan',
            'porsi_paket'  => '4 hari 2 kali makan (siang dan sore)',
            'total_hari'   => 4,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price'        => 400000,   // lebih rendah dari premium (650k)
        ]);
    }

    public function mingguanSatuKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket mingguan',
            'porsi_paket'  => '8 hari 1 kali makan (siang/malam saja)',
            'total_hari'   => 8,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price'        => 400000,   // lebih rendah dari premium (650k)
        ]);
    }

    // BULANAN (2 item)
    public function bulananDuaKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket bulanan',
            'porsi_paket'  => '12 hari 2 kali makan (siang dan sore)',
            'total_hari'   => 12,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price'        => 1180000,  // lebih rendah dari premium (1.95jt)
        ]);
    }

    public function bulananSatuKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket bulanan',
            'porsi_paket'  => '24 hari 1 kali makan (siang/malam saja)',
            'total_hari'   => 24,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price'        => 1180000,  // lebih rendah dari premium (1.95jt)
        ]);
    }

    // 3 BULANAN (2 item)
    public function tigaBulananDuaKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket 3 bulanan',
            'porsi_paket'  => '36 hari 2 kali makan (siang dan sore)',
            'total_hari'   => 36,
            'detail_paket' => 'makan (Siang dan Sore)',
            'price'        => 3540000,  // lebih rendah dari premium (5.83jt)
        ]);
    }

    public function tigaBulananSatuKaliReguler()
    {
        return $this->state(fn() => [
            'jenis_paket'  => 'paket 3 bulanan',
            'porsi_paket'  => '72 hari 1 kali makan (siang/malam saja)',
            'total_hari'   => 72,
            'detail_paket' => 'makan (Siang atau Malam)',
            'price'        => 3540000,  // lebih rendah dari premium (5.83jt)
        ]);
    }


    /* =========================
       OPSIONAL: HARIAN (kalau butuh)
       ========================= */

    // Harian Reguler -> "harga per porsi"
    public function harianReguler()
    {
        return $this->state(fn() => [
            'nama_meal_package' => 'paket reguler',
            'jenis_paket' => 'harian',
            'porsi_paket' => 'harga per porsi',
            'total_hari'  => 1,
            'detail_paket' => 'makan (per porsi)',
            'price' => 50000
        ]);
    }

    // Harian Premium -> "2 kali makan (siang dan malam)"
    public function harianPremium()
    {
        return $this->state(fn() => [
            'nama_meal_package' => 'paket ekspress',
            'jenis_paket' => 'harian',
            'porsi_paket' => '2 kali makan (siang dan malam)',
            'total_hari'  => 1,
            'detail_paket' => 'makan (Siang dan Malam)',
            'price' => 170000
        ]);
    }
}
