<?php

namespace App\Console\Commands;

use App\Models\MenuMakanan;
use App\Models\Order;
use App\Models\OrderDeliveryStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyDeliveryStatuses extends Command
{
    protected $signature = 'heartfit:generate-delivery-statuses {--date=} {--all}';
    protected $description = 'Generate pending delivery rows per order aktif pada tanggal tertentu';

    public function handle()
    {
        $tz         = 'Asia/Jakarta';
        $dateStr    = $this->option('date') ?: now($tz)->toDateString();
        $dayOfMonth = (int) Carbon::parse($dateStr)->day;

        // Ambil semua order PAID yang service_dates-nya mengandung tanggal ini
        $activeOrders = Order::where('status', 'PAID')
            ->whereJsonContains('service_dates', $dateStr)
            ->get(['id', 'meal_package_id', 'package_batch', 'unique_menus']);

        if ($activeOrders->isEmpty()) {
            $this->warn("{$dateStr} | Tidak ada order aktif.");
            return self::SUCCESS;
        }

        $inserted = 0;
        $skipped  = 0;

        foreach ($activeOrders as $order) {
            $batch     = $order->package_batch;
            $menuNames = is_array($order->unique_menus) ? $order->unique_menus : [];

            // Cari menu yang serve_days-nya mengandung tanggal hari ini
            $query = MenuMakanan::where('batch', $batch)
                ->whereIn('nama_menu', $menuNames);

            if (!$this->option('all')) {
                $query->whereJsonContains('serve_days', $dayOfMonth);
            }

            $menu = $query->first();

            if (!$menu) {
                $this->warn("Order #{$order->id}: tidak ada menu untuk hari ke-{$dayOfMonth} (batch {$batch})");
                continue;
            }

            // Cegah duplikat: 1 record per (meal_package_id, menu_makanan_id, delivery_date)
            $exists = OrderDeliveryStatus::where('meal_package_id', $order->meal_package_id)
                ->where('menu_makanan_id', $menu->id)
                ->where('delivery_date', $dateStr)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            OrderDeliveryStatus::create([
                'meal_package_id' => $order->meal_package_id,
                'menu_makanan_id' => $menu->id,
                'batch'           => $batch,
                'delivery_date'   => $dateStr,
                'status_siang'    => 'pending',
                'status_malam'    => 'pending',
                'confirmed_by'    => null,
                'confirmed_at'    => null,
                'note'            => null,
            ]);
            $inserted++;
        }

        $this->info("{$dateStr} | day: {$dayOfMonth} | orders aktif: {$activeOrders->count()} | inserted: {$inserted} | skipped: {$skipped}");
        return self::SUCCESS;
    }
}
