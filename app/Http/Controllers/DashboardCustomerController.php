<?php

namespace App\Http\Controllers;

use App\Models\OrderDeliveryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardCustomerController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = now($tz)->toDateString();

        $items = \App\Models\OrderDeliveryStatus::with(['mealPackage', 'menuMakanan'])
            ->whereDate('delivery_date', $date)
            ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->get();
        // dd($items);
        return view('customers.dashboard', compact('items', 'date'));
    }
}
