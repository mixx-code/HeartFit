<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Lepas FK order_id dulu
        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            // nama default FK Laravel: order_delivery_statuses_order_id_foreign
            // aman juga pakai array kolom:
            if (Schema::hasColumn('order_delivery_statuses', 'order_id')) {
                $table->dropForeign(['order_id']);
            }
        });

        // 2) Hapus unique lama (order_id, delivery_date)
        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            // nama auto: order_delivery_statuses_order_id_delivery_date_unique
            if ($this->indexExists('order_delivery_statuses', 'order_delivery_statuses_order_id_delivery_date_unique')) {
                $table->dropUnique('order_delivery_statuses_order_id_delivery_date_unique');
            }
        });

        // 3) Hapus kolom order_id
        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            if (Schema::hasColumn('order_delivery_statuses', 'order_id')) {
                $table->dropColumn('order_id');
            }
        });

        // 4) Tambah kolom & constraint baru
        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            $table->foreignId('meal_package_id')->after('id')
                ->constrained('meal_packages')->cascadeOnDelete();

            $table->foreignId('menu_makanan_id')->after('meal_package_id')
                ->constrained('menu_makanans')->cascadeOnDelete();

            if (!Schema::hasColumn('order_delivery_statuses', 'batch')) {
                $table->string('batch', 100)->nullable()->after('menu_makanan_id');
            }

            // unique baru: satu paket + menu + tanggal = satu baris
            $table->unique(['meal_package_id', 'menu_makanan_id', 'delivery_date'], 'ods_unique_pkg_menu_date');

            // index bantu
            $table->index('delivery_date');
        });
    }

    public function down(): void
    {
        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            // rollback: hapus unique & index baru
            if ($this->indexExists('order_delivery_statuses', 'ods_unique_pkg_menu_date')) {
                $table->dropUnique('ods_unique_pkg_menu_date');
            }
            if ($this->indexExists('order_delivery_statuses', 'order_delivery_statuses_delivery_date_index')) {
                $table->dropIndex('order_delivery_statuses_delivery_date_index');
            }

            // hapus FK baru
            $table->dropConstrainedForeignId('menu_makanan_id');
            $table->dropConstrainedForeignId('meal_package_id');

            // kembalikan order_id + unique lamanya
            $table->foreignId('order_id')->nullable()
                ->constrained('orders')->nullOnDelete()->after('id');

            $table->unique(['order_id', 'delivery_date']);
        });
    }

    // Helper kecil untuk cek ada/tidaknya index (biar migration idempotent)
    private function indexExists(string $table, string $index): bool
    {
        return collect(Schema::getConnection()->select(
            'SHOW INDEX FROM ' . $table . ' WHERE Key_name = ?',
            [$index]
        ))->isNotEmpty();
    }
};
