<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah opsi 'diproses' ke ENUM
        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang ENUM('pending','diproses','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam ENUM('pending','diproses','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert: hapus 'diproses' dari ENUM
        // Pastikan tidak ada nilai 'diproses' sebelum revert
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_siang = 'pending'
            WHERE status_siang = 'diproses'
        ");
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_malam = 'pending'
            WHERE status_malam = 'diproses'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang ENUM('pending','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam ENUM('pending','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
