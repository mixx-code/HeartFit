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
        // Update existing users to have proper roles if needed
        \DB::statement("UPDATE users SET role = 'admin' WHERE role NOT IN ('customer', 'admin', 'ahli_gizi', 'medical_record', 'bendahara', 'superadmin')");
        
        // Add check constraint for role field
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'customer', 'ahli_gizi', 'medical_record', 'bendahara'])
                  ->default('customer')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer', 'ahli_gizi', 'medical_record', 'bendahara'])
                  ->default('customer')
                  ->change();
        });
    }
};
