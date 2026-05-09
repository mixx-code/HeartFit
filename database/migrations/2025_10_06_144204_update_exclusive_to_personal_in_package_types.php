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
        // Update any existing 'Exclusive' package types to 'Personal'
        DB::table('package_types')
            ->where('packageType', 'Exclusive')
            ->update(['packageType' => 'Personal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'Personal' back to 'Exclusive' (only if they were originally 'Exclusive')
        DB::table('package_types')
            ->where('packageType', 'Personal')
            ->update(['packageType' => 'Exclusive']);
    }
};
