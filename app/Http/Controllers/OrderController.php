<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;


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
            'payment_method'    => ['required', Rule::in(['transfer', 'cod'])],
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

    /** Simpan order dari halaman preview (step akhir “Lihat Ringkasan”) */
    public function store(Request $request)
    {
        $validKeys = array_keys($this->packages);
        $data = $request->validate([
            'package_key'      => ['required', Rule::in($validKeys)],
            'package_label'    => ['required', 'string', 'max:150'],
            'package_category' => ['required', 'string', 'max:50'],
            'package_price'    => ['required', 'integer', 'min:0'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'days'             => ['required', 'integer', 'min:1'],
            'payment_method'   => ['required', Rule::in(['transfer', 'cod'])],
        ]);

        // generate order_number stabil
        $orderNumber = $this->generateOrderNumber();

        $order = Order::create([
            'order_number'    => $orderNumber,
            'user_id'         => Auth::id(),
            'package_key'     => $data['package_key'],
            'package_label'   => $data['package_label'],
            'package_category' => $data['package_category'],
            'package_price'   => $data['package_price'],
            'amount_total'    => $data['package_price'], // kalau ada pajak/ongkir, hitung di sini
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'days'            => $data['days'],
            'payment_method'  => $data['payment_method'],
            'status'          => 'UNPAID',
            'meta'            => null,
        ]);

        // Jika COD: langsung arahkan ke halaman finish dengan instruksi COD
        if ($order->payment_method === 'cod') {
            // di bisnis COD kamu bisa konfirmasi manual, dan set PAID saat serah terima
            return redirect()->route('orders.finish', $order);
        }

        // Kalau ewallet/bank_transfer → lanjut ke proses pay (buat attempt + token)
        return redirect()->route('orders.pay', $order);
    }

    /** Buat attempt pembayaran dan kirim Snap token ke view */
    public function pay(Order $order, MidtransService $svc)
    {
        // hitung attempt berikutnya
        $attempt = $order->paymentTransactions()->count() + 1;
        $midtransOrderId = "{$order->order_number}-{$attempt}";

        $params = [
            'transaction_details' => [
                'order_id'      => $midtransOrderId,
                'gross_amount'  => (int) ($order->amount_total ?? $order->package_price),
            ],
            'customer_details' => [
                'first_name' => optional(Auth::user())->name ?? 'Guest',
                'email'      => optional(Auth::user())->email ?? 'user@example.com',
            ],
            'callbacks' => [
                'finish' => route('orders.finish', $order),
            ],
            'expiry' => ['unit' => 'minutes', 'duration' => 60],
            // kamu bisa batasi payment channel sesuai payment_method yang dipilih
        ];

        $token = Snap::getSnapToken($params);

        // catat attempt pending
        $tx = $order->paymentTransactions()->create([
            'midtrans_order_id'   => $midtransOrderId,
            'attempt'             => $attempt,
            'transaction_status'  => 'pending',
            'gross_amount'        => (int) ($order->amount_total ?? $order->package_price),
        ]);

        return view('customers.orders.pay', [
            'order'     => $order,
            'snapToken' => $token,
            'attempt'   => $attempt,
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }

    /** Halaman selesai (UX). Status final tetap percaya webhook. */
    public function finish(Order $order)
    {
        // Tampilkan ringkasan + status terkini dari DB
        return view('customers.orders.finish', compact('order'));
    }

    /** Webhook Midtrans (notifikasi server → sumber status final) */
    public function webhook(Request $req)
    {
        $serverKey = config('services.midtrans.server_key');

        // Validasi signature (v2): sha512(order_id + status_code + gross_amount + serverKey)
        $calc = hash('sha512', $req->order_id . $req->status_code . $req->gross_amount . $serverKey);
        if (!hash_equals($calc, (string) $req->signature_key)) {
            abort(403, 'Invalid signature');
        }

        /** @var PaymentTransaction|null $tx */
        $tx = PaymentTransaction::where('midtrans_order_id', $req->order_id)->first();
        if (!$tx) {
            // Lindungi dari duplicate/late notification yg datang sebelum attempt tercatat
            // atau order_id tidak dikenali
            return response()->json(['ignored' => true], 200);
        }

        // Update transaksi
        $tx->update([
            'transaction_id'     => $req->transaction_id ?? $tx->transaction_id,
            'payment_type'       => $req->payment_type ?? $tx->payment_type,
            'transaction_status' => $req->transaction_status,
            'fraud_status'       => $req->fraud_status ?? null,
            'gross_amount'       => (int) $req->gross_amount,
            'signature_key'      => $req->signature_key,
            'raw_notification'   => $req->all(),
            'settled_at'         => in_array($req->transaction_status, ['capture', 'settlement']) ? now() : $tx->settled_at,
        ]);

        // Sinkron status order
        $order = $tx->order;
        switch ($req->transaction_status) {
            case 'capture':
            case 'settlement':
                $order->update(['status' => 'PAID', 'paid_at' => now()]);
                break;
            case 'expire':
                $order->update(['status' => 'EXPIRED']);
                break;
            case 'cancel':
                $order->update(['status' => 'CANCELED']);
                break;
            case 'deny':
            case 'failure':
            default:
                // tetap UNPAID; user bisa retry
                break;
        }

        return response()->json(['ok' => true]);
    }

    /** Helper: generator order_number */
    private function generateOrderNumber(): string
    {
        // format: ORD-YYYYMMDD-xxxxxx (increment harian sederhana)
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $rand   = strtoupper(Str::padLeft((string) random_int(1, 999999), 6, '0'));
        return $prefix . $rand;
    }

    // OrderController@confirm
    public function confirm(Request $request, Order $order)
    {
        $data = $request->validate([
            'midtrans_order_id' => ['required', 'string'],
        ]);

        // Normalisasi order_id yang masuk dari client
        $incoming = trim($data['midtrans_order_id']);

        // Jika client kirim tanpa suffix "-attempt", fallback ke attempt terakhir
        if ($incoming === $order->order_number) {
            $lastAttempt = (int) ($order->paymentTransactions()->max('attempt') ?? 1);
            $incoming = "{$order->order_number}-{$lastAttempt}";
        }

        // Safety check: pastikan prefix cocok dengan order yg sedang dikonfirmasi
        if (!Str::startsWith($incoming, $order->order_number . '-')) {
            Log::warning('Confirm refused: order_id prefix mismatch', [
                'order_id'   => $order->id,
                'incoming'   => $incoming,
                'expected'   => $order->order_number . '-*',
            ]);
            return response()->json([
                'ok'      => false,
                'message' => 'Order ID tidak cocok dengan order ini.',
            ], 422);
        }

        // Ambil attempt dari suffix
        $attemptStr = Str::afterLast($incoming, '-');
        $attempt    = ctype_digit($attemptStr) ? (int) $attemptStr : 1;

        // Panggil Midtrans untuk status aktual
        try {
            $status = Transaction::status($incoming); // stdClass
        } catch (\Exception $e) {
            Log::warning('Midtrans status error', [
                'order_id' => $order->id,
                'incoming' => $incoming,
                'error'    => $e->getMessage(),
            ]);
            return response()->json([
                'ok'      => false,
                'message' => 'Midtrans status error: ' . $e->getMessage(),
                'hint'    => 'Umumnya karena transaksi belum dibuat (popup ditutup) atau ENV/serverKey tidak sesuai.',
            ], 422);
        }

        // Tulisan status mentah ke log (membantu debug saat sandbox)
        Log::info('Midtrans status ok', [
            'order_id' => $order->id,
            'incoming' => $incoming,
            'status'   => $status,
        ]);

        // Update atomik agar konsisten
        DB::transaction(function () use ($order, $incoming, $attempt, $status) {

            // Upsert PaymentTransaction berbasis $incoming (BUKAN data mentah dari request)
            /** @var \App\Models\PaymentTransaction|null $tx */
            $tx = $order->paymentTransactions()
                ->where('midtrans_order_id', $incoming)
                ->first();

            if (!$tx) {
                $tx = $order->paymentTransactions()->create([
                    'midtrans_order_id'  => $incoming,
                    'attempt'            => $attempt,
                    'transaction_status' => $status->transaction_status ?? 'pending',
                    'gross_amount'       => (int) (($status->gross_amount ?? $order->amount_total) ?: $order->package_price),
                ]);
            }

            $tx->update([
                'transaction_id'     => $status->transaction_id  ?? $tx->transaction_id,
                'payment_type'       => $status->payment_type     ?? $tx->payment_type,
                'transaction_status' => $status->transaction_status ?? $tx->transaction_status,
                'fraud_status'       => $status->fraud_status     ?? $tx->fraud_status,
                'gross_amount'       => isset($status['gross_amount']) ? (int) $status['gross_amount'] : $tx->gross_amount,
                // signature_key biasanya tidak ada di endpoint status; biarkan null/eksisting
                'raw_notification'   => json_decode(json_encode($status), true),
                'settled_at'         => in_array(($status->transaction_status ?? ''), ['capture', 'settlement'])
                    ? now()
                    : $tx->settled_at,
            ]);

            // === Sinkron Order ===
            $trx   = $status->transaction_status ?? '';
            $fraud = $status->fraud_status ?? null;

            if (($trx === 'capture' && $fraud !== 'challenge') || $trx === 'settlement') {
                $order->update(['status' => 'PAID', 'paid_at' => now()]);
            } elseif ($trx === 'expire') {
                $order->update(['status' => 'EXPIRED']);
            } elseif ($trx === 'cancel') {
                $order->update(['status' => 'CANCELED']);
            } else {
                // pending / deny / challenge / failure -> biarkan UNPAID
                // Webhook tetap akan jadi source of truth final
            }
        });

        // Balikkan status terbaru ke client
        $fresh = $order->fresh();
        return response()->json([
            'ok'        => true,
            'order_id'  => $fresh->id,
            'status'    => $fresh->status,
            'paid_at'   => optional($fresh->paid_at)?->toDateTimeString(),
            'order_no'  => $fresh->order_number,
            'attempt'   => $attempt,
            'checked'   => $incoming,
        ]);
    }

    public function snapResult(Request $request, Order $order)
    {
        // simpan payload front-end untuk audit/debug
        $payload = $request->all();
        $meta = $order->meta ?? [];
        $meta['last_snap_result'] = $payload;
        $order->update(['meta' => $meta]);

        return response()->json(['ok' => true]);
    }
}
