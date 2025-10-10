<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'superadmin@mail.com'],
            [
                'name'     => 'superadmin',
                'password' => Hash::make('superadmin123'),
                'role'     => 'admin',
            ]
        );

        UserDetail::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'mr'            => 'MR-001',
                'nik'           => '3201123456789001',
                'alamat'        => 'Jl. Raya Admin No.1',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1990-01-01',
                'bb_tb'         => '70/175',
                'foto_ktp_base64' => null, // bisa isi base64 kalau mau
                'hp'            => '081234567890',
                'usia'          => 34,
                'created_by'    => $admin->id,
            ]
        );

        // Customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@mail.com'],
            [
                'name'     => 'customer',
                'password' => Hash::make('customer123'),
                'role'     => 'customer',
            ]
        );

        UserDetail::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'mr'            => 'MR-002',
                'nik'           => '3201123456789002',
                'alamat'        => 'Jl. Raya Customer No.2',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Bandung',
                'tanggal_lahir' => '1995-05-05',
                'bb_tb'         => '55/160',
                'foto_ktp_base64' => null,
                'hp'            => '081298765432',
                'usia'          => 29,
                'created_by'    => $admin->id, // misalnya admin yang buat
            ]
        );

        // Seeder lainnya
        $this->call([
            PackageTypeSeeder::class, // wajib duluan
            MealPackagesSeeder::class,
            MenuMakananSeeder::class
        ]);
    }
}
