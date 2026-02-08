<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class MRGeneratorService
{
    /**
     * Generate MR number dengan format DDMMYYYY + 3 digit random
     * Contoh: 08022026001
     */
    public static function generate(): string
    {
        $datePart = Carbon::now()->format('dmY');
        $randomPart = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return $datePart . $randomPart;
    }

    /**
     * Generate MR number yang unik (cek di database)
     */
    public static function generateUnique(): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $mr = self::generate();
            $exists = \App\Models\UserDetail::where('mr', $mr)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            throw new \Exception('Failed to generate unique MR number after multiple attempts');
        }

        return $mr;
    }
}
