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
        Schema::table('menu_makanans', function (Blueprint $table) {
            $table->json('foto_makanan')->nullable()->after('spec_menu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_makanans', function (Blueprint $table) {
            $table->dropColumn('foto_makanan');
        });
    }
};
