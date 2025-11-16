<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('heartfit:generate-delivery-statuses')
    ->dailyAt('00:03')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// sementara untuk test saja:
// Schedule::command('heartfit:generate-delivery-statuses')
//     ->everyMinute()
//     ->timezone('Asia/Jakarta');
