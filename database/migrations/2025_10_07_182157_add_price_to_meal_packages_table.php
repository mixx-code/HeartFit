<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_packages', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->after('detail_paket');
        });
    }

    public function down(): void
    {
        Schema::table('meal_packages', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
