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
        // === ADMIN ===
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
                'mr'              => 'MR-001',
                'nik'             => '3201123456789001',
                'alamat'          => 'Jl. Raya Admin No.1',
                'jenis_kelamin'   => 'L',
                'tempat_lahir'    => 'Jakarta',
                'tanggal_lahir'   => '1990-01-01',
                'bb_tb'           => '70/175',
                'foto_ktp_base64' => null,
                'hp'              => '081234567890',
                'usia'            => 34,
                'created_by'      => $admin->id,
            ]
        );

        // === CUSTOMER ===
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
                'mr'              => 'MR-002',
                'nik'             => '3201123456789002',
                'alamat'          => 'Jl. Raya Customer No.2',
                'jenis_kelamin'   => 'P',
                'tempat_lahir'    => 'Bandung',
                'tanggal_lahir'   => '1995-05-05',
                'bb_tb'           => '55/160',
                'foto_ktp_base64' => null,
                'hp'              => '081298765432',
                'usia'            => 29,
                'created_by'      => $admin->id,
            ]
        );

        // === MEDICAL RECORD ===
        $medical = User::updateOrCreate(
            ['email' => 'medical@mail.com'],
            [
                'name'     => 'medical_record',
                'password' => Hash::make('medical123'),
                'role'     => 'medical_record',
            ]
        );

        UserDetail::updateOrCreate(
            ['user_id' => $medical->id],
            [
                'mr'              => 'MR-003',
                'nik'             => '3201123456789003',
                'alamat'          => 'Jl. Sehat No.3',
                'jenis_kelamin'   => 'L',
                'tempat_lahir'    => 'Depok',
                'tanggal_lahir'   => '1992-03-12',
                'bb_tb'           => '68/172',
                'foto_ktp_base64' => null,
                'hp'              => '081311223344',
                'usia'            => 33,
                'created_by'      => $admin->id,
            ]
        );

        // === BENDAHARA ===
        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@mail.com'],
            [
                'name'     => 'bendahara',
                'password' => Hash::make('bendahara123'),
                'role'     => 'bendahara',
            ]
        );

        UserDetail::updateOrCreate(
            ['user_id' => $bendahara->id],
            [
                'mr'              => 'MR-004',
                'nik'             => '3201123456789004',
                'alamat'          => 'Jl. Keuangan No.4',
                'jenis_kelamin'   => 'P',
                'tempat_lahir'    => 'Bogor',
                'tanggal_lahir'   => '1991-07-22',
                'bb_tb'           => '60/165',
                'foto_ktp_base64' => null,
                'hp'              => '081322334455',
                'usia'            => 34,
                'created_by'      => $admin->id,
            ]
        );

        // === AHLI GIZI ===
        $gizi = User::updateOrCreate(
            ['email' => 'gizi@mail.com'],
            [
                'name'     => 'ahli_gizi',
                'password' => Hash::make('gizi123'),
                'role'     => 'ahli_gizi',
            ]
        );

        UserDetail::updateOrCreate(
            ['user_id' => $gizi->id],
            [
                'mr'              => 'MR-005',
                'nik'             => '3201123456789005',
                'alamat'          => 'Jl. Nutrisi No.5',
                'jenis_kelamin'   => 'P',
                'tempat_lahir'    => 'Bekasi',
                'tanggal_lahir'   => '1994-10-10',
                'bb_tb'           => '58/162',
                'foto_ktp_base64' => null,
                'hp'              => '081355667788',
                'usia'            => 31,
                'created_by'      => $admin->id,
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
