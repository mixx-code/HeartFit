<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    protected $signature = 'user:list {--role= : Filter by role} {--show-password : Show password status}';
    protected $description = 'List all users with optional role filter';

    protected $roles = [
        'admin' => 'Administrator',
        'ahli_gizi' => 'Ahli Gizi',
        'customer' => 'Customer',
        'kurir' => 'Kurir',
    ];

    public function handle()
    {
        $roleFilter = $this->option('role');
        
        $query = User::query();

        if ($roleFilter) {
            if (!array_key_exists($roleFilter, $this->roles)) {
                $this->error("Role '{$roleFilter}' tidak valid. Role yang tersedia:");
                foreach ($this->roles as $key => $label) {
                    $this->line("  - {$key}: {$label}");
                }
                return self::FAILURE;
            }
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('role')->orderBy('name')->get();

        if ($users->isEmpty()) {
            $this->info('Tidak ada user ditemukan.');
            return self::SUCCESS;
        }

        $this->info('Daftar User:');
        $this->line(str_repeat('-', 60));

        foreach ($users as $user) {
            $roleLabel = $this->roles[$user->role] ?? $user->role;
            $status = $user->deleted_at ? '(Dihapus)' : '';
            $passwordStatus = $this->option('show-password') ? '| Pass: ✓ ' : '';
            
            $this->line(sprintf(
                "ID: %-4d | Name: %-20s | Email: %-25s | Role: %-12s %s %s",
                $user->id,
                $user->name,
                $user->email,
                $roleLabel,
                $status,
                $passwordStatus
            ));
        }

        $this->line(str_repeat('-', 60));
        $this->info("Total: {$users->count()} user");

        return self::SUCCESS;
    }
}
