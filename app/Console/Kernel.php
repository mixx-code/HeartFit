<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // jalan tiap hari jam 05:00 WIB
        // $schedule->command('heartfit:generate-delivery-statuses')
        //     ->dailyAt('05:00')
        //     ->timezone('Asia/Jakarta')
        //     ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
