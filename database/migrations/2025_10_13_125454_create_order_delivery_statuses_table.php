<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_delivery_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Tanggal pengantaran yang direncanakan (per hari)
            $table->date('delivery_date');

            // Status pengantaran siang & malam (pakai Bahasa Indonesia)
            $table->enum('status_siang', ['pending', 'sedang dikirim', 'sampai', 'gagal dikirim'])
                ->default('pending');
            $table->enum('status_malam', ['pending', 'sedang dikirim', 'sampai', 'gagal dikirim'])
                ->default('pending');

            // Opsional: siapa yang konfirmasi & kapan
            $table->foreignId('confirmed_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();

            // Catatan opsional (misalnya alasan gagal)
            $table->text('note')->nullable();

            $table->timestamps();

            // 1 order hanya boleh punya 1 baris per tanggal
            $table->unique(['order_id', 'delivery_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_delivery_statuses');
    }
};
