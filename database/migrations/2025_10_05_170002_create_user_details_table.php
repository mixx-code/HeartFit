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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id(); // Id Detail User (AI)

            // relasi 1:1 ke users
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('mr')->unique();            // Medical Record
            $table->string('nik', 32)->unique();       // NIK
            $table->text('alamat');                    // Alamat
            $table->enum('jenis_kelamin', ['L', 'P']);  // L = laki, P = perempuan

            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');

            $table->string('bb_tb')->nullable();       // contoh: "70/170"
            $table->longText('foto_ktp_base64')->nullable();
            $table->string('hp', 30)->nullable();
            $table->unsignedSmallInteger('usia')->nullable();

            // audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
