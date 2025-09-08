<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('package_key');
            $table->string('package_label');
            $table->string('package_category');
            $table->unsignedInteger('package_price'); // dalam rupiah

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('days');

            $table->string('payment_method'); // bank_transfer | ewallet | cod
            $table->json('meta')->nullable(); // data tambahan (alamat, catatan, dsb)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
