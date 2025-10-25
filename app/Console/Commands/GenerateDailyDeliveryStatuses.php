<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDailyDeliveryStatuses extends Command
{
    protected $signature = 'heartfit:generate-delivery-statuses {--date=} {--all}';
    protected $description = 'Generate pending (siang & malam) delivery rows per menu_makanan (1x per menu, bukan per meal_package)';

    public function handle()
    {
        $tz   = 'Asia/Jakarta';
        $date = $this->option('date') ?: now($tz)->toDateString();

        // Nama hari Indonesia & angka ISO (1=Senin..7=Minggu)
        $hariNama  = now($tz)->locale('id')->isoFormat('dddd'); // "Senin", "Selasa", ...
        $hariAngka = (int) now($tz)->isoWeekday();              // 1..7

        /**
         * Subquery representative meal_package per batch:
         * satu id per batch (pakai MIN), untuk memenuhi FK meal_package_id
         */
        $mpPerBatch = DB::table('meal_packages')
            ->selectRaw('batch, MIN(id) AS meal_package_id')
            ->groupBy('batch');

        // Base: 1 row per menu_makanan (join ke representative meal_package per batch)
        $base = DB::table('menu_makanans as mm')
            ->joinSub($mpPerBatch, 'mpb', function ($join) {
                $join->on('mm.batch', '=', 'mpb.batch');
            });

        // Filter serve_days jika tidak --all
        if (!$this->option('all')) {
            $base->where(function ($q) use ($hariNama, $hariAngka) {
                // serve_days bisa berisi ["Senin","Rabu"] ATAU [1,3,5]
                $q->orWhereRaw('JSON_CONTAINS(mm.serve_days, JSON_QUOTE(?))', [$hariNama])
                    ->orWhereRaw('JSON_CONTAINS(mm.serve_days, ?)', [json_encode($hariAngka)]);
            });
        }

        // SELECT kolom yang akan diinsert + NOT EXISTS untuk cegah duplikat per menu_makanan+tanggal
        $select = $base->selectRaw(
            'mpb.meal_package_id AS meal_package_id,
             mm.id               AS menu_makanan_id,
             mm.batch            AS batch,
             ?                   AS delivery_date,
             "pending"           AS status_siang,
             "pending"           AS status_malam,
             NULL                AS confirmed_by,
             NULL                AS confirmed_at,
             NULL                AS note,
             NOW()               AS created_at,
             NOW()               AS updated_at',
            [$date]
        )
            ->whereNotExists(function ($sub) use ($date) {
                $sub->from('order_delivery_statuses as ods')
                    ->selectRaw('1')
                    ->whereColumn('ods.menu_makanan_id', 'mm.id')
                    ->where('ods.delivery_date', $date);
            });

        // Eksekusi insertUsing: hasilnya 1 baris per menu_makanan (bukan per meal_package)
        $inserted = DB::table('order_delivery_statuses')->insertUsing([
            'meal_package_id',
            'menu_makanan_id',
            'batch',
            'delivery_date',
            'status_siang',
            'status_malam',
            'confirmed_by',
            'confirmed_at',
            'note',
            'created_at',
            'updated_at'
        ], $select);

        $this->info("{$date} | hari: {$hariNama} ({$hariAngka}) | inserted: {$inserted} (per menu_makanan)");
        return self::SUCCESS;
    }
}
