<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    protected $signature = 'user:create {email} {role=admin}';
    protected $description = 'Create a new user with auto-generated name and default password';

    protected $roles = [
        'superadmin'     => 'Super Administrator',
        'admin'          => 'Administrator',
        'ahli_gizi'      => 'Ahli Gizi',
        'medical_record' => 'Medical Record',
        'bendahara'      => 'Bendahara',
        'kurir'          => 'Kurir',
        'customer'       => 'Customer',
    ];

    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');
        
        // Generate nama dari email (hilangkan @domain.com)
        $name = explode('@', $email)[0];
        
        // Default password
        $password = 'password123';

        // Validasi role
        if (!array_key_exists($role, $this->roles)) {
            $this->error("Role '{$role}' tidak valid. Role yang tersedia:");
            foreach ($this->roles as $key => $label) {
                $this->line("  - {$key}: {$label}");
            }
            return self::FAILURE;
        }

        // Validasi input
        $validator = Validator::make([
            'email' => $email,
        ], [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            $this->error('Validasi gagal:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - {$error}");
            }
            return self::FAILURE;
        }

        // Cek apakah email sudah ada
        if (User::withTrashed()->where('email', $email)->exists()) {
            $this->error("Email '{$email}' sudah digunakan.");
            return self::FAILURE;
        }

        // Buat user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'created_by' => 1, // Default ke admin pertama
        ]);

        $this->info("User berhasil dibuat:");
        $this->line("  Name: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Password: password123");
        $this->line("  Role: {$this->roles[$user->role]} ({$user->role})");
        $this->line("  ID: {$user->id}");

        return self::SUCCESS;
    }
}
