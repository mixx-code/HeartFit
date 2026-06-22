<?php

namespace App\Http\Controllers;

use App\Models\OrderDeliveryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = $request->get('date', now($tz)->toDateString());

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

        $delivered  = $items->where('status_siang', 'sampai')->count() + $items->where('status_malam', 'sampai')->count();
        $totalSlots = $items->count() * 2;
        $kpi = [
            'orders_today'    => \App\Models\Order::where('status', 'PAID')
                                    ->whereJsonContains('service_dates', $date)
                                    ->count(),
            'delivered'       => $delivered,
            'delivered_pct'   => $totalSlots > 0 ? round($delivered / $totalSlots * 100) : 0,
            'shipping'        => $items->where('status_siang', 'sedang dikirim')->count() + $items->where('status_malam', 'sedang dikirim')->count(),
            'failed'          => $items->where('status_siang', 'gagal dikirim')->count() + $items->where('status_malam', 'gagal dikirim')->count(),
        ];

        return view('admin.dashboard', compact('items', 'date', 'agg', 'history', 'kpi'));
    }

    public function updateStatus(Request $request, OrderDeliveryStatus $delivery)
    {
        $allowed = config('settings.delivery.update_status', []);
        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status delivery.');
        }

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

    public function generateDelivery(Request $request)
    {
        $allowed = config('settings.delivery.generate', []);
        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Anda tidak memiliki akses untuk membuat delivery.');
        }

        $tz   = 'Asia/Jakarta';
        $date = $request->input('date', now($tz)->toDateString());

        $exitCode = Artisan::call('heartfit:generate-delivery-statuses', [
            '--date' => $date,
        ]);

        $output = trim(Artisan::output());

        if ($exitCode === 0) {
            return redirect()
                ->route('dashboard.admin', ['date' => $date])
                ->with('success', "Generate delivery berhasil");
        }

        return redirect()
            ->route('dashboard.admin', ['date' => $date])
            ->with('error', "Generate delivery gagal");
    }
}
