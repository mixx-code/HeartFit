<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_packages', function (Blueprint $table) {
            $table->id();
            $table->string('batch')->nullable();
            $table->enum('jenis_paket', ['harian', 'mingguan', 'bulanan']);
            $table->string('porsi_paket');  // ex: "4 Hari / 8 Hari"
            $table->string('detail_paket'); // ex: "2 Kali Makan (Siang & Sore)"
            $table->foreignId('package_type_id')->constrained('package_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_packages');
    }
};
