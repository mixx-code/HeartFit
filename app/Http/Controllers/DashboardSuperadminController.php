<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDeliveryStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardSuperadminController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = $request->get('date', now($tz)->toDateString());

        // Delivery section (sama seperti admin, tapi view-only)
        $items = OrderDeliveryStatus::with(['mealPackage', 'menuMakanan', 'confirmer'])
            ->whereDate('delivery_date', $date)
            ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->get();

        // Riwayat pengantaran (semua delivery sebelum tanggal dipilih)
        $history = OrderDeliveryStatus::with(['mealPackage', 'menuMakanan', 'confirmer'])
            ->whereDate('delivery_date', '<', $date)
            ->orderByDesc('delivery_date')
            ->get();

        // Ahli gizi section
        $q       = $request->input('q');
        $perPage = (int) $request->input('per_page', 10);

        $orders = Order::query()
            ->whereHas('user', fn($q) => $q->where('role', 'customer'))
            ->where('package_category', 'personal')
            ->with(['user:id,name,email', 'user.detail:user_id,mr,nik,hp,alamat'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_number', 'like', "%{$q}%")
                        ->orWhere('package_label', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $today   = Carbon::today();
        $summary = [
            'total_customers'   => User::where('role', 'customer')->count(),
            'total_orders'      => Order::count(),
            'active_today'      => Order::where('status', 'PAID')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'orders_this_month' => Order::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->count(),
        ];

        return view('superadmin.dashboard', compact('items', 'date', 'orders', 'perPage', 'summary', 'history'));
    }
}
