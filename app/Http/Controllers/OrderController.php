<?php

namespace App\Http\Controllers;

use App\Models\MealPackages;
use App\Models\MenuMakanan;
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
        $packages = MealPackages::with('packageType')
            ->where('batch', 'I')
            ->orderBy('created_at', 'desc')
            ->get();

        $packagesMap = $packages->mapWithKeys(function ($p) {
            return [
                $p->id => [
                    'id'           => $p->id,
                    'label'        => $p->nama_meal_package,
                    'category'     => optional($p->packageType)->packageType ?? 'Lainnya',
                    'price'        => (int) round($p->price),
                    'durationDays' => (int) ($p->total_hari ?? 0),
                    'batch'        => $p->batch,
                ],
            ];
        })->toArray();

        $menusBatchI = MenuMakanan::where('batch', 'I')
            ->get(['id', 'nama_menu', 'serve_days', 'spec_menu'])
            ->map(function ($m) {
                $serve = is_array($m->serve_days) ? $m->serve_days : [];
                $serve = array_values(array_filter(array_map(fn($v) => (int) $v, $serve), fn($n) => $n >= 1 && $n <= 31));

                return [
                    'id'         => $m->id,
                    'nama_menu'  => $m->nama_menu,
                    'serve_days' => $serve,                  // <- array angka siap pakai
                    'spec_menu'  => $m->spec_menu ?? [],     // <- array asosiatif (section => [items])
                ];
            })
            ->values()
            ->toArray();

        return view('customers.orders.create', compact('packages', 'packagesMap', 'menusBatchI'));
    }

    // public function create()
    // {
    //     // Ambil semua meal_packages dengan batch 'I'
    //     $packages = MealPackages::with('packageType')
    //         ->where('batch', 'X')
    //         ->get()
    //         ->groupBy(function ($item) {
    //             // Kelompokkan berdasarkan nama tipe paket, misalnya "Reguler" atau "Premium"
    //             return $item->packageType->packageType ?? 'Lainnya';
    //         });

    //     return view('customers.orders.create', compact('packages'));
    // }
    public function preview(Request $request)
    {
        // VALIDASI: gunakan exists ke tabel meal_packages
        $data = $request->validate([
            'package_key'        => ['required', 'integer', 'exists:meal_packages,id'],
            'start_date'         => ['required', 'date'],
            'end_date'           => ['required', 'date', 'after_or_equal:start_date'],
            'payment_method'     => ['required', Rule::in(['transfer', 'cod'])],

            // hidden (opsional)
            'package_label'      => ['nullable', 'string', 'max:150'],
            'package_category'   => ['nullable', 'string', 'max:50'],
            'package_batch'      => ['nullable', 'string', 'max:100'],
            'package_price'      => ['nullable', 'integer', 'min:0'],
            'amount_total'       => ['nullable', 'integer', 'min:0'],

            // step 3 arrays (boleh JSON/array)
            'service_dates'      => ['nullable'],
            'unique_menus'       => ['nullable'],
            'unique_menu_count'  => ['nullable', 'integer', 'min:0'],
        ]);

        // AMBIL DARI DB (bukan dari $this->packages)
        $pkg = MealPackages::with('packageType:id,packageType')
            ->select('id', 'nama_meal_package', 'price', 'total_hari', 'package_type_id')
            ->findOrFail($data['package_key']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();
        $days  = $start->diffInDays($end) + 1;

        // Normalisasi service_dates
        $serviceDates = $data['service_dates'] ?? [];
        if (is_string($serviceDates)) {
            $serviceDates = json_decode($serviceDates, true) ?: [];
        }
        $serviceDates = array_values(array_filter(array_map(function ($d) {
            $d = trim((string)$d);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;
        }, (array)$serviceDates)));

        // Normalisasi unique_menus (trim + dedupe CI)
        $uniqueMenus = $data['unique_menus'] ?? [];
        if (is_string($uniqueMenus)) {
            $uniqueMenus = json_decode($uniqueMenus, true) ?: [];
        }
        $seen = [];
        $uniqueMenus = array_values(array_filter(array_map(function ($n) use (&$seen) {
            $n = trim((string)$n);
            if ($n === '') return null;
            $k = mb_strtolower($n);
            if (isset($seen[$k])) return null;
            $seen[$k] = true;
            return $n;
        }, (array)$uniqueMenus)));
        $uniqueMenuCount = (int)($data['unique_menu_count'] ?? count($uniqueMenus));

        // Fallback label/kategori/harga dari DB bila hidden kosong
        $label    = $data['package_label']    ?? $pkg->nama_meal_package;
        $category = $data['package_category'] ?? (optional($pkg->packageType)->packageType ?? '-');
        $batch    = $data['package_batch']    ?? null;
        $price    = (int)($data['package_price'] ?? $pkg->price);
        $amount   = (int)($data['amount_total']  ?? $price);

        $summary = [
            'package_key'        => $pkg->id,
            'package_label'      => $label,
            'package_category'   => $category,
            'package_batch'      => $batch,

            'package_price'      => $price,
            'amount_total'       => $amount,

            'start_date'         => $start->toDateString(),
            'end_date'           => $end->toDateString(),
            'days'               => $days,

            'payment_method'     => $data['payment_method'],

            'service_dates'      => $serviceDates,
            'unique_menus'       => $uniqueMenus,
            'unique_menu_count'  => $uniqueMenuCount,
        ];

        $json = json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // kalau view butuh $pkg, kirim juga (opsional)
        return view('customers.orders.preview', compact('summary', 'json'));
    }


    /** Simpan order dari halaman preview (step akhir “Lihat Ringkasan”) */
    public function store(Request $request)
    {
        // --- validasi field utama (sesuai Step 1 & 2) ---
        // $validKeys = array_keys($this->packages); // kalau sdh pakai DB map, ganti sesuai kebutuhan
        $data = $request->validate([
            'package_key'        => ['required', 'integer', 'exists:meal_packages,id'], // <-- ganti ini
            'package_label'      => ['required', 'string', 'max:150'],
            'package_category'   => ['required', 'string', 'max:50'],
            'package_batch'      => ['nullable', 'string', 'max:100'],

            'package_price'      => ['required', 'integer', 'min:0'],
            'amount_total'       => ['nullable', 'integer', 'min:0'],

            'start_date'         => ['required', 'date'],
            'end_date'           => ['required', 'date', 'after_or_equal:start_date'],
            'days'               => ['required', 'integer', 'min:1'],

            'payment_method'     => ['required', Rule::in(['transfer', 'cod'])],

            'service_dates'      => ['nullable'],
            'unique_menus'       => ['nullable'],
            'unique_menu_count'  => ['nullable', 'integer', 'min:0'],
            'meta'               => ['nullable'],
        ]);

        // --- normalisasi incoming JSON string → array ---
        $serviceDates = $data['service_dates'] ?? [];
        if (is_string($serviceDates)) {
            $serviceDates = json_decode($serviceDates, true) ?: [];
        }
        // pastikan array string Y-m-d
        $serviceDates = array_values(array_filter(array_map(function ($d) {
            $d = trim((string)$d);
            // format sederhana: YYYY-MM-DD (biar aman)
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;
        }, (array)$serviceDates)));

        $uniqueMenus = $data['unique_menus'] ?? [];
        if (is_string($uniqueMenus)) {
            $uniqueMenus = json_decode($uniqueMenus, true) ?: [];
        }
        // trim + dedupe case-insensitive
        $seen = [];
        $uniqueMenus = array_values(array_filter(array_map(function ($name) use (&$seen) {
            $n = trim((string)$name);
            if ($n === '') return null;
            $key = mb_strtolower($n);
            if (isset($seen[$key])) return null;
            $seen[$key] = true;
            return $n;
        }, (array)$uniqueMenus)));

        $uniqueMenuCount = (int)($data['unique_menu_count'] ?? count($uniqueMenus));

        // meta (boleh JSON string/array)
        $meta = $data['meta'] ?? [];
        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?: [];
        }

        // --- konsistensi periode: hitung ulang total hari layanan dari start..end ---
        $start = new \DateTimeImmutable($data['start_date']);
        $end   = new \DateTimeImmutable($data['end_date']);
        $diffDays = $start->diff($end)->days + 1; // inclusive
        // jika "days" tidak cocok, pakai yang dihitung
        $days = max(1, (int)$data['days']);
        if ($days !== $diffDays) {
            $days = $diffDays;
        }

        // --- total: fallback ke package_price bila amount_total kosong ---
        $amountTotal = (int)($data['amount_total'] ?? $data['package_price']);

        // --- generate order number ---
        $orderNumber = $this->generateOrderNumber(); // sudah ada di kodinganmu

        // --- rakit meta tambahan opsional (berguna buat invoice) ---
        $meta = array_merge([
            'period' => $start->format('Y-m-d') . ' s/d ' . $end->format('Y-m-d'),
            'preview_menu_top' => array_slice($uniqueMenus, 0, 8),
        ], $meta);

        // --- simpan order ---
        $order = Order::create([
            'order_number'      => $orderNumber,
            'user_id'           => Auth::id(),

            'package_key'       => $data['package_key'],
            'package_label'     => $data['package_label'],
            'package_category'  => $data['package_category'],
            'package_batch'     => $data['package_batch'] ?? null,

            'package_price'     => (int)$data['package_price'],
            'amount_total'      => $amountTotal,

            'start_date'        => $start->format('Y-m-d'),
            'end_date'          => $end->format('Y-m-d'),
            'days'              => $days,

            'service_dates'     => $serviceDates,
            'unique_menus'      => $uniqueMenus,
            'unique_menu_count' => $uniqueMenuCount,

            'payment_method'    => $data['payment_method'],
            'status'            => 'UNPAID',
            'meta'              => $meta,
        ]);

        // --- alur redirect sama seperti sebelumnya ---
        if ($order->payment_method === 'cod') {
            return redirect()->route('orders.finish', $order);
        }
        return redirect()->route('orders.pay', $order);
    }

    public function pay(Order $order, MidtransService $svc)
    {
        // attempt & midtrans order id
        $attempt = $order->paymentTransactions()->count() + 1;
        $midtransOrderId = "{$order->order_number}-{$attempt}";

        // ====== Ringkasan Step 3 (diringkas ke custom_field*) ======
        $periodText = ($order->start_date && $order->end_date)
            ? $order->start_date->format('Y-m-d') . ' s/d ' . $order->end_date->format('Y-m-d')
            : '-';

        // daftar tanggal layanan (max 15 biar singkat)
        $dates = array_slice((array)$order->service_dates, 0, 15);
        $datesText = implode(',', $dates);
        if (count((array)$order->service_dates) > 15) {
            $datesText .= ' (+more)';
        }

        // menu unik (max 10 biar singkat)
        $menus = array_slice((array)$order->unique_menus, 0, 10);
        $menusText = implode(', ', $menus);
        if ($order->unique_menu_count > 10) {
            $menusText .= ' (+more)';
        }

        // custom fields (≤ 255 chars)
        $cf1 = mb_substr("{$order->package_label} | {$order->package_category} | Batch: " . ($order->package_batch ?? '-'), 0, 255);
        $cf2 = mb_substr("Periode: {$periodText} | Durasi: {$order->days} hari | Tgl: {$datesText}", 0, 255);
        $cf3 = mb_substr("Menu unik ({$order->unique_menu_count}): {$menusText}", 0, 255);

        $gross = (int) ($order->amount_total ?? $order->package_price);

        $params = [
            'transaction_details' => [
                'order_id'       => $midtransOrderId,
                'gross_amount'   => $gross,

                // taruh ringkasan tahap 3 di custom_field*
                'custom_field1'  => $cf1, // paket | kategori | batch
                'custom_field2'  => $cf2, // periode | durasi | tanggal layanan (ringkas)
                'custom_field3'  => $cf3, // menu unik (ringkas)
            ],

            'item_details' => [
                [
                    'id'       => (string) $order->package_key,
                    'price'    => $gross,
                    'quantity' => 1,
                    'name'     => mb_substr($order->package_label, 0, 50), // aman ≤ 50
                    'category' => mb_substr($order->package_category, 0, 50),
                ],
            ],

            'customer_details' => [
                'first_name' => optional(Auth::user())->name ?? 'Guest',
                'email'      => optional(Auth::user())->email ?? 'user@example.com',
                // (opsional) kalau punya phone/address, bisa tambahkan:
                // 'phone' => $order->meta['phone'] ?? null,
                // 'billing_address' => [...],
                // 'shipping_address' => [...],
            ],

            // payment channels
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
                'unit'     => 'minutes',
                'duration' => 1,
            ],
        ];

        $token = Snap::getSnapToken($params);

        // log attempt pending
        $order->paymentTransactions()->create([
            'midtrans_order_id'   => $midtransOrderId,
            'attempt'             => $attempt,
            'transaction_status'  => 'pending',
            'gross_amount'        => $gross,
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
        // --- 1) Validasi signature v2 ---
        $serverKey = config('services.midtrans.server_key');
        $orderId   = (string) $req->input('order_id');
        $status    = (string) $req->input('status_code');
        $grossStr  = (string) $req->input('gross_amount'); // gunakan apa adanya (string)
        $sign      = (string) $req->input('signature_key');

        $calcSign  = hash('sha512', $orderId . $status . $grossStr . $serverKey);
        if (!hash_equals($calcSign, $sign)) {
            Log::warning('[Midtrans Webhook] Invalid signature', ['order_id' => $orderId]);
            abort(403, 'Invalid signature');
        }

        // --- 2) Ambil PaymentTransaction by midtrans_order_id ---
        /** @var PaymentTransaction|null $tx */
        $tx = PaymentTransaction::where('midtrans_order_id', $orderId)->first();
        if (!$tx) {
            // notifikasi bisa datang “lebih cepat” dari catatan attempt; abaikan dengan 200 supaya Midtrans tidak retry berlebihan
            Log::warning('[Midtrans Webhook] Transaction not found for order_id', ['order_id' => $orderId]);
            return response()->json(['ignored' => true], 200);
        }

        $order = $tx->order; // relasi ke Order
        if (!$order) {
            Log::warning('[Midtrans Webhook] Order not found for transaction', ['order_id' => $orderId, 'tx_id' => $tx->id]);
            return response()->json(['ignored' => true], 200);
        }

        // --- 3) Normalisasi field penting dari notif ---
        $notif = $req->all();
        $transactionStatus = (string) ($notif['transaction_status'] ?? '');
        $paymentType       = (string) ($notif['payment_type'] ?? ''); // bca_va, qris, gopay, dana, etc
        $fraudStatus       = $notif['fraud_status'] ?? null;
        $transactionId     = $notif['transaction_id'] ?? $tx->transaction_id;

        // gross_amount dari midtrans bisa "10000.00" (string)
        $grossAsInt = (int) floor((float) $grossStr);

        // VA detail (bentuknya bisa array va_numbers)
        $vaNumbers   = $notif['va_numbers'] ?? null;     // array of [{bank, va_number}]
        $permataVa   = $notif['permata_va_number'] ?? null;
        $billKey     = $notif['bill_key'] ?? null;
        $billerCode  = $notif['biller_code'] ?? null;

        // QRIS/e-wallet detail (opsional)
        $acquirer    = $notif['acquirer'] ?? null;
        $currency    = $notif['currency'] ?? 'IDR';

        // custom_field dari payload pay()
        $custom1 = $notif['custom_field1'] ?? null;
        $custom2 = $notif['custom_field2'] ?? null;
        $custom3 = $notif['custom_field3'] ?? null;

        // --- 4) Update transaksi (idempoten) ---
        $tx->update([
            'transaction_id'     => $transactionId,
            'payment_type'       => $paymentType,
            'transaction_status' => $transactionStatus,
            'fraud_status'       => $fraudStatus,
            'gross_amount'       => $grossAsInt,
            'signature_key'      => $sign,
            'raw_notification'   => $notif,
            'settled_at'         => in_array($transactionStatus, ['capture', 'settlement']) ? now() : $tx->settled_at,
            // simpan ekstra ke meta tx agar mudah dilihat
            'meta'               => array_merge((array)($tx->meta ?? []), [
                'currency'     => $currency,
                'va_numbers'   => $vaNumbers,
                'permata_va'   => $permataVa,
                'bill_key'     => $billKey,
                'biller_code'  => $billerCode,
                'acquirer'     => $acquirer,
                'custom_field' => [
                    '1' => $custom1,
                    '2' => $custom2,
                    '3' => $custom3,
                ],
            ]),
        ]);

        // --- 5) Verifikasi nominal (opsional tapi bagus) ---
        $expected = (int) ($order->amount_total ?? $order->package_price);
        if ($grossAsInt !== $expected) {
            // tandai di meta order agar bisa ditinjau
            $order->update([
                'meta' => array_merge((array)($order->meta ?? []), [
                    'amount_mismatch' => [
                        'expected' => $expected,
                        'actual'   => $grossAsInt,
                        'at'       => now()->toAtomString(),
                    ],
                ]),
            ]);
            Log::warning('[Midtrans Webhook] Amount mismatch', [
                'order_id' => $orderId,
                'expected' => $expected,
                'actual' => $grossAsInt
            ]);
        }

        // --- 6) Sinkron status Order ---
        // mapping:
        //  - settlement / capture  => PAID
        //  - refund / partial_refund => REFUNDED
        //  - expire => EXPIRED
        //  - cancel => CANCELED
        //  - pending/deny/failure => tetap UNPAID (user bisa retry)
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                if ($order->status !== 'PAID') {
                    $order->update([
                        'status'  => 'PAID',
                        'paid_at' => now(),
                        // set payment_method online kalau sebelumnya COD (harusnya tidak terjadi untuk jalur midtrans)
                        'payment_method' => $order->payment_method === 'cod' ? 'transfer' : $order->payment_method,
                    ]);
                }
                break;

            case 'refund':
            case 'partial_refund':
                $order->update(['status' => 'REFUNDED']);
                break;

            case 'expire':
                $order->update(['status' => 'EXPIRED']);
                break;

            case 'cancel':
                $order->update(['status' => 'CANCELED']);
                break;

            case 'pending':
            case 'deny':
            case 'failure':
            default:
                // biarkan UNPAID; tidak perlu update paid_at
                if ($order->status === 'PAID' && in_array($transactionStatus, ['deny', 'failure'])) {
                    // Jangan turunkan status PAID ke UNPAID; cukup catat
                    Log::info('[Midtrans Webhook] Received non-success after PAID; ignoring status downgrade', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus
                    ]);
                }
                break;
        }

        // --- 7) Response OK supaya Midtrans stop retry ---
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
