<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahan untuk informasi paket
            $table->string('package_batch')->nullable()->after('package_category');

            // Data ringkasan Step 3
            $table->json('service_dates')->nullable()->after('days');
            $table->json('unique_menus')->nullable()->after('service_dates');
            $table->unsignedInteger('unique_menu_count')->default(0)->after('unique_menus');

            // Index tambahan untuk query rentang tanggal
            $table->index(['start_date', 'end_date'], 'orders_period_idx');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_period_idx');
            $table->dropColumn(['package_batch', 'service_dates', 'unique_menus', 'unique_menu_count']);
        });
    }
};
