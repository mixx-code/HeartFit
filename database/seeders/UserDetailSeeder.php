<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserDetailSeeder extends Seeder
{
    public function run(): void
    {
        // kosongkan dulu (untuk pengembangan)
        Schema::disableForeignKeyConstraints();
        UserDetail::truncate();
        Schema::enableForeignKeyConstraints();

        // bikin detail untuk semua user yang belum punya
        User::query()
            ->where('role', 'customer') // optional filter
            ->get()
            ->each(function (User $user) {
                UserDetail::factory()->for($user)->create();
            });
    }
}
