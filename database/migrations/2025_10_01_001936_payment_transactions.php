<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // id order Midtrans per attempt (unik)
            $table->string('midtrans_order_id')->unique(); // ex: ORD-20251001-000123-1
            $table->string('transaction_id')->nullable();  // dari Midtrans
            $table->unsignedInteger('attempt')->default(1);

            $table->string('payment_type')->nullable();    // bank_transfer, qris, gopay, dll
            $table->string('transaction_status')->default('pending'); // pending|capture|settlement|deny|cancel|expire|failure
            $table->string('fraud_status')->nullable();    // accept|challenge|deny
            $table->unsignedBigInteger('gross_amount')->nullable();

            $table->json('extra')->nullable();             // VA list, qr_string, pdf_url, dll
            $table->string('signature_key')->nullable();
            $table->json('raw_notification')->nullable();

            $table->timestamp('expired_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->string('failed_reason')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'transaction_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
