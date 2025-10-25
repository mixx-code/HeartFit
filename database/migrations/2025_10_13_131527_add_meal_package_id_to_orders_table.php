<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('meal_package_id')->nullable()
                ->constrained('meal_packages')->nullOnDelete()->after('user_id');

            $table->index('package_label'); // bantu backfill
        });
    }
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meal_package_id');
            $table->dropIndex(['package_label']);
        });
    }
};
