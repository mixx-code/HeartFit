<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class BlockOrderWindowFromDB
{
    public function handle(Request $request, Closure $next)
    {
        $tz       = config('app.timezone', 'Asia/Jakarta');

        // YYYY-MM-DD untuk aman di DATE/DATETIME
        $today    = Carbon::now($tz)->toDateString();
        $tomorrow = Carbon::now($tz)->addDay()->toDateString();

        $userId = Auth::id();

        // Blokir jika (today in range) ATAU (tomorrow in range)
        $exists = Order::query()
            ->where('user_id', $userId) // hapus baris ini jika mau global
            // ->whereIn('status', ['PAID'])
            ->where(function ($q) use ($today, $tomorrow) {
                $q->where(function ($q2) use ($today) {
                    $q2->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date',   '>=', $today);
                })
                    ->orWhere(function ($q3) use ($tomorrow) {
                        $q3->whereDate('start_date', '<=', $tomorrow)
                            ->whereDate('end_date',   '>=', $tomorrow);
                    });
            })
            ->exists();

        // (opsional) debug cepat
        Log::info('Block check OR', compact('today','tomorrow','userId','exists'));

        if ($exists) {
            $role = Auth::user()->role ?? 'customer';

            // tentukan rute dashboard sesuai role
            $dashboard = match ($role) {
                'admin', 'ahli_gizi', 'bendahara', 'medical_record' => 'dashboard.admin',
                default => 'dashboard.customer',
            };

            return redirect()
                ->route($dashboard)
                ->with('warning', 'Pembuatan pesanan tidak tersedia karena periode aktif masih berlangsung.');
        }


        return $next($request);
    }
}
