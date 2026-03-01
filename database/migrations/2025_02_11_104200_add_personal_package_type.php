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
        // First update any existing 'Exclusive' to 'Personal'
        DB::table('package_types')
            ->where('packageType', 'Exclusive')
            ->update(['packageType' => 'Personal']);
        
        // Then insert 'Personal' if it doesn't exist
        DB::table('package_types')->insertOrIgnore([
            'packageType' => 'Personal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Personal' entries
        DB::table('package_types')
            ->where('packageType', 'Personal')
            ->delete();
    }
};
