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

    public function index()
    {
        $q = request('q');
        $perPage = (int) request('per_page', 10);

        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->when($q, function ($query) use ($q) {
                $query->where('order_number', 'like', "%{$q}%")
                    ->orWhere('package_label', 'like', "%{$q}%");
            })
            ->latest('id')
            ->paginate($perPage);

        return view('customers.orders.index', compact('orders', 'perPage'));
    }


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
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) ($order->amount_total ?? $order->package_price),
            ],

            'item_details' => [
                [
                    'id'       => $order->package_key,
                    'price'    => (int) ($order->amount_total ?? $order->package_price),
                    'quantity' => 1,
                    'name'     => $order->package_label,
                    'category' => $order->package_category,
                ],
            ],

            'customer_details' => [
                'first_name' => optional(Auth::user())->name ?? 'Guest',
                'email'      => optional(Auth::user())->email ?? 'user@example.com',
            ],

            // 🔑 Hanya tampilkan bank VA BCA/BNI/BRI/Mandiri + QRIS + e-wallet (OVO, GoPay, DANA)
            'enabled_payments' => [
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_va',
                'qris',
                'ovo',
                'gopay',
                'dana',
            ],

            'callbacks' => [
                'finish' => route('orders.finish', $order),
            ],
            'expiry' => [
                'unit' => 'minutes',
                'duration' => 60,
            ],
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
    public function webhook(Request $req, MidtransService $svc)
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
    public function confirm(Request $request, Order $order, MidtransService $svc)
    {
        $data = $request->validate(['midtrans_order_id' => ['required', 'string']]);

        $incoming = trim($data['midtrans_order_id']);
        if ($incoming === $order->order_number) {
            $lastAttempt = (int) ($order->paymentTransactions()->max('attempt') ?? 1);
            $incoming = "{$order->order_number}-{$lastAttempt}";
        }
        if (!Str::startsWith($incoming, $order->order_number . '-')) {
            Log::warning('Confirm refused: order_id prefix mismatch', ['order_id' => $order->id, 'incoming' => $incoming]);
            return response()->json(['ok' => false, 'message' => 'Order ID tidak cocok dengan order ini.'], 422);
        }

        $attemptStr = Str::afterLast($incoming, '-');
        $attempt    = ctype_digit($attemptStr) ? (int) $attemptStr : 1;

        try {
            $status = Transaction::status($incoming); // stdClass (atau dianggap array oleh analyzer)
        } catch (\Exception $e) {
            Log::warning('Midtrans status error', ['order_id' => $order->id, 'incoming' => $incoming, 'error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Midtrans status error: ' . $e->getMessage()], 422);
        }

        Log::info('Midtrans status ok', ['order_id' => $order->id, 'incoming' => $incoming, 'status' => $status]);

        DB::transaction(function () use ($order, $incoming, $attempt, $status) {
            $tx = $order->paymentTransactions()
                ->where('midtrans_order_id', $incoming)
                ->first();

            if (!$tx) {
                $tx = $order->paymentTransactions()->create([
                    'midtrans_order_id'  => $incoming,
                    'attempt'            => $attempt,
                    'transaction_status' => data_get($status, 'transaction_status', 'pending'),
                    'gross_amount'       => (int) ((data_get($status, 'gross_amount')
                        ?? $order->amount_total)
                        ?: $order->package_price),
                ]);
            }

            $trxStatus = data_get($status, 'transaction_status', $tx->transaction_status);
            $fraud     = data_get($status, 'fraud_status',     $tx->fraud_status);
            $payment   = data_get($status, 'payment_type',     $tx->payment_type);

            // --- RANGKUM EXTRA (VA / QR / PDF, dsb) ---
            $extra = $tx->extra ?? [];
            $vaNumbers = data_get($status, 'va_numbers');                 // BCA/BNI/BRI {[{bank,va_number}]}
            $permataVa = data_get($status, 'permata_va_number');          // Permata
            $billKey   = data_get($status, 'bill_key');                   // Mandiri
            $billerCode = data_get($status, 'biller_code');                // Mandiri
            $actions   = data_get($status, 'actions');                    // e-wallet actions (deeplink/pdf)
            $qrString  = data_get($status, 'qr_string');                  // QRIS string (jika ada)
            $pdfUrl    = collect((array) $actions)->firstWhere('name', 'pdf_url')['url'] ?? null;

            $extra['payment_type'] = $payment;
            if ($vaNumbers) $extra['va_numbers'] = $vaNumbers;
            if ($permataVa) $extra['permata_va_number'] = $permataVa;
            if ($billKey)   $extra['bill_key']   = $billKey;
            if ($billerCode) $extra['biller_code'] = $billerCode;
            if ($actions)   $extra['actions']    = $actions;
            if ($qrString)  $extra['qr_string']  = $qrString;
            if ($pdfUrl)    $extra['pdf_url']    = $pdfUrl;

            // Expiry (kalau Midtrans mengembalikan)
            $expiryTime = data_get($status, 'expiry_time'); // beberapa channel pakai field ini (format epoch/ISO)
            $expiredAt  = null;
            if ($expiryTime) {
                // coba parse ke datetime laravel
                try {
                    $expiredAt = \Carbon\Carbon::parse($expiryTime);
                } catch (\Throwable $e) {
                }
            }

            $tx->update([
                'transaction_id'     => data_get($status, 'transaction_id',  $tx->transaction_id),
                'payment_type'       => $payment,
                'transaction_status' => $trxStatus,
                'fraud_status'       => $fraud,
                'gross_amount'       => (int) (data_get($status, 'gross_amount', $tx->gross_amount)),
                'extra'              => $extra,
                'raw_notification'   => json_decode(json_encode($status), true),
                'expired_at'         => $expiredAt ?: $tx->expired_at,
                'settled_at'         => in_array($trxStatus, ['capture', 'settlement']) ? now() : $tx->settled_at,
            ]);

            // === Sinkron ke Orders ===
            if (($trxStatus === 'capture' && $fraud !== 'challenge') || $trxStatus === 'settlement') {
                $order->update(['status' => 'PAID', 'paid_at' => now()]);
            } elseif ($trxStatus === 'expire') {
                $order->update(['status' => 'EXPIRED']);
            } elseif ($trxStatus === 'cancel') {
                $order->update(['status' => 'CANCELED']);
            } elseif ($trxStatus === 'pending') {
                // PENDING: tandai eksplisit (tetap UNPAID) + (opsional) simpan meta ringkas untuk kemudahan tampilan
                $meta = $order->meta ?? [];
                $meta['last_pending'] = [
                    'payment_type' => $payment,
                    'va_numbers'   => $vaNumbers,
                    'permata'      => $permataVa,
                    'bill_key'     => $billKey,
                    'biller_code'  => $billerCode,
                    'qr_string'    => $qrString,
                    'pdf_url'      => $pdfUrl,
                    'expired_at'   => optional($expiredAt)?->toIso8601String(),
                ];
                $order->update(['status' => 'UNPAID', 'meta' => $meta]);
            }
        });


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

    public function statusJson(Order $order)
    {
        return response()->json([
            'id'          => $order->id,
            'order_no'    => $order->order_number,
            'status'      => $order->status,
            'paid_at'     => optional($order->paid_at)?->toDateTimeString(),
            'updated_at'  => $order->updated_at->toDateTimeString(),
        ]);
    }
}
