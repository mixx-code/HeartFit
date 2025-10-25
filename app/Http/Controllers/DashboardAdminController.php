<?php

namespace App\Http\Controllers;

use App\Models\OrderDeliveryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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

        return view('admin.dashboard', compact('items', 'date', 'agg'));
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
