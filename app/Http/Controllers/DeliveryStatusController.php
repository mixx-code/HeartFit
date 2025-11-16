<?php

// app/Http/Controllers/DeliveryStatusController.php
namespace App\Http\Controllers;

use App\Events\DeliveryStatusUpdated;
use App\Models\OrderDeliveryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeliveryStatusController extends Controller
{
    // List hari ini (admin/petugas lihat semua)
    // app/Http/Controllers/DeliveryStatusController.php

    public function index(Request $request)
    {
        $date = $request->query('date', now('Asia/Jakarta')->toDateString());

        $items = OrderDeliveryStatus::with(['order', 'confirmer'])
            ->whereDate('delivery_date', $date)
            // urutkan pending duluan untuk siang, lalu malam
            ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->latest('id')
            ->paginate(20);

        return view('delivery_status.index', compact('items', 'date'));
    }

    // Tambahkan dua action khusus
    public function confirmSiang(OrderDeliveryStatus $deliveryStatus, Request $request)
    {
        $deliveryStatus->update([
            'status_siang' => 'sampai',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'note'         => $request->input('note'),
        ]);

        $this->broadcastDeliveryStatus($deliveryStatus, 'siang', 'sampai', $request->input('note'));

        return back()->with('status', 'Status siang diubah ke sampai.');
    }

    public function confirmMalam(OrderDeliveryStatus $deliveryStatus, Request $request)
    {
        $deliveryStatus->update([
            'status_malam' => 'sampai',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'note'         => $request->input('note'),
        ]);

        $this->broadcastDeliveryStatus($deliveryStatus, 'malam', 'sampai', $request->input('note'));

        return back()->with('status', 'Status malam diubah ke sampai.');
    }

    public function confirmDikirimSiang(OrderDeliveryStatus $deliveryStatus, Request $request)
    {
        if (in_array($deliveryStatus->status_siang, ['sampai', 'gagal dikirim'])) {
            return back()->with('status', 'Status siang sudah final dan tidak dapat diubah.');
        }

        $deliveryStatus->update([
            'status_siang' => 'sedang dikirim',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'note'         => $request->input('note'),
        ]);

        $this->broadcastDeliveryStatus($deliveryStatus, 'siang', 'sedang dikirim', $request->input('note'));

        return back()->with('status', 'Status siang diubah ke sedang dikirim.');
    }

    public function confirmDikirimMalam(OrderDeliveryStatus $deliveryStatus, Request $request)
    {
        if (in_array($deliveryStatus->status_malam, ['sampai', 'gagal dikirim'])) {
            return back()->with('status', 'Status malam sudah final dan tidak dapat diubah.');
        }

        $deliveryStatus->update([
            'status_malam' => 'sedang dikirim',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'note'         => $request->input('note'),
        ]);

        $this->broadcastDeliveryStatus($deliveryStatus, 'malam', 'sedang dikirim', $request->input('note'));

        return back()->with('status', 'Status malam diubah ke sedang dikirim.');
    }

    private function broadcastDeliveryStatus($deliveryStatus, string $shift, string $newStatus, ?string $note = null): void
    {
        $order = $deliveryStatus->order;
        if (!$order) {
            Log::warning('Order not found for delivery status', [
                'delivery_status_id' => $deliveryStatus->id
            ]);
            return;
        }

        // ✅ DEBUG DETAILED
        Log::info('🎯 BROADCASTING DELIVERY STATUS', [
            'delivery_status_id' => $deliveryStatus->id,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'current_auth_id' => Auth::id(),
            'shift' => $shift,
            'status' => $newStatus,
            'channel' => 'user.' . $order->user_id,
            'note' => $note
        ]);

        try {
            broadcast(new DeliveryStatusUpdated(
                userId: (int) $order->user_id,
                deliveryStatusId: (int) $deliveryStatus->id,
                orderId: (int) $order->id,
                shift: Str::lower($shift),
                status: Str::lower($newStatus),
                date: $deliveryStatus->delivery_date?->toDateString() ?? now('Asia/Jakarta')->toDateString(),
                note: $note
            ))->toOthers();

            Log::info('✅ EVENT BROADCASTED SUCCESSFULLY');
        } catch (\Exception $e) {
            Log::error('❌ FAILED TO BROADCAST EVENT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
