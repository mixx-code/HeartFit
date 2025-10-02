<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // identitas & relasi user (nullable kalau guest checkout)
            $table->string('order_number')->unique();   // ex: ORD-20251001-000123
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // paket
            $table->string('package_key');
            $table->string('package_label');
            $table->string('package_category');
            $table->unsignedInteger('package_price');

            // total (opsional — kalau mau)
            $table->unsignedBigInteger('amount_total')->nullable();

            // periode
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('days');

            // pembayaran
            $table->string('payment_method'); // bank_transfer | ewallet | cod
            $table->enum('status', ['UNPAID', 'PAID', 'CANCELED', 'EXPIRED', 'REFUNDED'])->default('UNPAID');
            $table->timestamp('paid_at')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['paid_at']);
            $table->dropColumn(['order_number', 'user_id', 'status', 'amount_total', 'paid_at']);
        });
    }
};
