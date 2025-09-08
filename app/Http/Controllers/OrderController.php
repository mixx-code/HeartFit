<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OrderController extends Controller
{
    // mapping statis paket (tanpa DB)
    private array $packages = [
        'reguler_harian'   => ['label' => 'Reguler (Harian)',    'category' => 'Reguler', 'price' =>  50000],
        'reguler_mingguan' => ['label' => 'Mingguan (Reguler)',  'category' => 'Reguler', 'price' => 400000],
        'reguler_bulanan'  => ['label' => 'Bulanan (Reguler)',   'category' => 'Reguler', 'price' => 1180000],
        'reguler_3bulanan' => ['label' => '3 Bulanan (Reguler)', 'category' => 'Reguler', 'price' => 3540000],
        'premium_ekspress' => ['label' => 'Ekspress (Premium)',  'category' => 'Premium', 'price' => 170000],
        'premium_mingguan' => ['label' => 'Mingguan (Premium)',  'category' => 'Premium', 'price' => 650000],
        'premium_bulanan'  => ['label' => 'Bulanan (Premium)',   'category' => 'Premium', 'price' => 1950000],
        'premium_3bulanan' => ['label' => '3 Bulanan (Premium)', 'category' => 'Premium', 'price' => 5830000],
    ];

    public function create()
    {
        $packages = $this->packages;
        return view('customers.orders.create', compact('packages'));
    }

    public function preview(Request $request)
    {
        $validKeys = array_keys($this->packages);

        $data = $request->validate([
            'package_key'       => ['required', Rule::in($validKeys)],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['required', 'date', 'after_or_equal:start_date'],
            'payment_method'    => ['required', Rule::in(['bank_transfer', 'ewallet', 'cod'])],
            // field hidden dari JS opsional—boleh nggak dikirim
            'package_label'     => ['nullable', 'string', 'max:150'],
            'package_category'  => ['nullable', 'string', 'max:50'],
            'package_price'     => ['nullable', 'integer', 'min:0'],
        ]);

        $pkg   = $this->packages[$data['package_key']];
        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $days  = $start->diffInDays($end) + 1;

        // ringkasan (tanpa simpan ke DB)
        $summary = [
            'package_key'      => $data['package_key'],
            'package_label'    => $pkg['label'],
            'package_category' => $pkg['category'],
            'package_price'    => $pkg['price'],
            'start_date'       => $start->toDateString(),
            'end_date'         => $end->toDateString(),
            'days'             => $days,
            'payment_method'   => $data['payment_method'],
        ];

        // Bisa juga kirim sebagai JSON buat keperluan frontend
        $json = json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return view('customers.orders.preview', compact('summary', 'json', 'pkg'));
    }
}
