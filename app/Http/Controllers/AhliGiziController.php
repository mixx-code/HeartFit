<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AhliGiziController extends Controller
{
    /**
     * Dashboard Ahli Gizi - Tampilkan order customer paket personal
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perPage = (int) $request->input('per_page', 10);

        $orders = Order::query()
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->where('package_category', 'personal')
            ->with([
                'user:id,name,email',
                'user.detail:user_id,mr,nik,hp'
            ])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('order_number', 'like', "%{$q}%")
                        ->orWhere('package_label', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $today = Carbon::today();

        $summary = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_orders'    => Order::count(),
            'active_today'    => Order::where('status', 'PAID')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'orders_this_month' => Order::whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->count(),
        ];

        return view('ahli_gizi.orders.index', compact('orders', 'perPage', 'summary'));
    }

    /**
     * Redirect ke WA customer
     */
    public function redirectToWa($userId)
    {
        $user = User::with('detail')->findOrFail($userId);
        
        // Ambil nomor HP dari user_details
        $phoneNumber = $user->detail->hp ?? null;
        
        if (!$phoneNumber) {
            return back()->with('error', 'Customer tidak memiliki nomor HP.');
        }

        // Format nomor HP untuk WhatsApp
        $waNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Redirect ke WhatsApp
        $waUrl = "https://wa.me/62" . ltrim($waNumber, '0');
        
        return redirect()->away($waUrl);
    }
}
