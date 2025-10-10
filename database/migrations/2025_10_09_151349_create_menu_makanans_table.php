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
        Schema::create('menu_makanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu');
            $table->string('batch')->nullable(); // contoh: I, II, III
            $table->json('spec_menu');           // {"Makan Siang":[...], "Makan Malam":[...]}

            // Tambahan kolom user tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();   // created_at & updated_at
            $table->softDeletes();  // deleted_at (untuk soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_makanans');
    }
};
