<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDeliveryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = $request->get('date', now($tz)->toDateString());

        $items = OrderDeliveryStatus::with(['mealPackage', 'menuMakanan'])
            ->whereDate('delivery_date', $date)
            ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->get();

        // agregasi (biar ada ringkasan cepat, kamu suka %)
        $total = max(1, $items->count());
        $agg = [
            'siang' => [
                'pending'        => $items->where('status_siang', 'pending')->count(),
                'sedang dikirim' => $items->where('status_siang', 'sedang dikirim')->count(),
                'sampai'         => $items->where('status_siang', 'sampai')->count(),
                'gagal dikirim'  => $items->where('status_siang', 'gagal dikirim')->count(),
            ],
            'malam' => [
                'pending'        => $items->where('status_malam', 'pending')->count(),
                'sedang dikirim' => $items->where('status_malam', 'sedang dikirim')->count(),
                'sampai'         => $items->where('status_malam', 'sampai')->count(),
                'gagal dikirim'  => $items->where('status_malam', 'gagal dikirim')->count(),
            ],
            'total' => $total
        ];

        // ===== RINGKASAN USER AKTIF/TIDAK AKTIF (berdasarkan order terbaru per user) =====
        $today = Carbon::parse($date, $tz)->toDateString();

        // Subquery: ambil timestamp terbaru per user (prioritas paid_at, fallback created_at)
        $latestPerUser = Order::query()
            ->select('user_id', DB::raw('MAX(COALESCE(paid_at, created_at)) as last_ts'))
            ->groupBy('user_id');
        // ->where('status', 'PAID') // <- aktifkan jika mau hanya order berstatus PAID

        // Join ke orders untuk ambil rentang tanggal dari order terbaru
        $latestOrders = Order::query()
            ->from('orders as o')
            ->joinSub($latestPerUser, 'lu', function ($join) {
                $join->on('o.user_id', '=', 'lu.user_id')
                    ->on(DB::raw('COALESCE(o.paid_at, o.created_at)'), '=', 'lu.last_ts');
            })
            ->select('o.user_id', 'o.start_date', 'o.end_date');

        // Total user unik yg punya order (berdasarkan order terbaru)
        $uniqueUsers = (clone $latestOrders)
            ->distinct()
            ->count('o.user_id');

        // Aktif: today berada dalam [start_date, end_date]
        $activeUserCount = (clone $latestOrders)
            ->whereDate('o.start_date', '<=', $today)
            ->whereDate('o.end_date', '>=', $today)
            ->distinct()
            ->count('o.user_id');

        // Tidak aktif: sisanya
        $inactiveUserCount = max(0, $uniqueUsers - $activeUserCount);

        return view('admin.dashboard', compact('items', 'date', 'agg', 'activeUserCount', 'inactiveUserCount'));
    }

    public function updateStatus(Request $request, OrderDeliveryStatus $delivery)
    {
        // field = 'status_siang' | 'status_malam'
        // value = salah satu enum
        $validated = $request->validate([
            'field' => ['required', Rule::in(['status_siang', 'status_malam'])],
            'value' => ['required', Rule::in(['pending', 'diproses', 'sedang dikirim', 'sampai', 'gagal dikirim'])],
            'note'  => ['nullable', 'string']
        ]);

        $field = $validated['field'];
        $delivery->{$field} = $validated['value'];
        $delivery->confirmed_by = Auth::id();
        $delivery->confirmed_at = now('Asia/Jakarta');
        if (array_key_exists('note', $validated)) {
            $delivery->note = $validated['note'];
        }
        $delivery->save();

        return redirect()
            ->route('dashboard.admin')
            ->with('success', 'Status pengantaran berhasil diperbarui.');
    }
}
