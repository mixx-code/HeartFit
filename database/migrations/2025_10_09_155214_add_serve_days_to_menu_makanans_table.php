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
            $table->json('serve_days')->nullable()->after('spec_menu');
        });
    }

    public function down(): void
    {
        Schema::table('menu_makanans', function (Blueprint $table) {
            $table->dropColumn('serve_days');
        });
    }
};
