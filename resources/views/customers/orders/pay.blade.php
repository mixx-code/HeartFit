@extends('layouts.app')
@section('title', 'Bayar Pesanan')

@section('content')
    @push('styles')
        <style>
            /* Card detail yang rapi */
            .kv-card .kv-row+.kv-row {
                border-top: 1px dashed #eef2f7
            }

            .kv-label {
                flex: 0 0 160px;
                max-width: 160px;
                font-size: .8rem;
                text-transform: uppercase;
                letter-spacing: .04em;
                color: var(--bs-secondary-color);
            }

            .kv-value {
                flex: 1;
                color: var(--bs-emphasis-color);
                font-weight: 600
            }

            .kv-mono {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace
            }

            .badge-soft {
                background: #f6f9ff;
                border: 1px solid #e5edff;
                color: #3b82f6;
                font-weight: 600;
            }

            .kv-title {
                letter-spacing: .02em
            }
        </style>
    @endpush

    @php
        use Carbon\Carbon;
        $start = $order->start_date instanceof \Carbon\Carbon ? $order->start_date : Carbon::parse($order->start_date);
        $end = $order->end_date instanceof \Carbon\Carbon ? $order->end_date : Carbon::parse($order->end_date);

        $methodLabel = strtoupper(str_replace('_', ' ', $order->payment_method ?? '-'));
        $grandTotal = (int) ($order->amount_total ?? ($order->package_price ?? 0));

        $statusClass = match ($order->status) {
            'PAID' => 'bg-success',
            'PENDING' => 'bg-info',
            'FAILED' => 'bg-danger',
            'EXPIRED' => 'bg-secondary',
            default => 'bg-warning',
        };

        $serviceDates = is_string($order->service_dates)
            ? json_decode($order->service_dates, true)
            : $order->service_dates ?? [];
        $menusRaw = is_string($order->unique_menus)
            ? json_decode($order->unique_menus, true)
            : $order->unique_menus ?? [];
        $menus = [];

        if (is_array($menusRaw)) {
            foreach ($menusRaw as $m) {
                $menus[] = is_array($m) ? $m['name'] ?? ($m['nama'] ?? json_encode($m)) : (string) $m;
            }
        }
    @endphp

    <div class="container py-5 d-flex justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold text-primary mb-4 text-center">Pembayaran</h3>

            {{-- === DETAIL PESANAN === --}}
            <div class="card border-0 shadow-sm mb-4 kv-card">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3 text-center kv-title">Rincian Pesanan</h5>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Nomor Order</div>
                        <div class="kv-value kv-mono">{{ $order->order_number }}</div>
                    </div>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Paket</div>
                        <div class="kv-value fw-semibold">
                            {{ $order->package_label }}
                            <span class="text-muted fw-normal">({{ $order->package_category }})</span>
                            @if (!empty($order->package_batch))
                                <span class="badge badge-soft rounded-pill ms-1">BATCH: {{ $order->package_batch }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Periode</div>
                        <div class="kv-value fw-semibold">
                            {{ $start->toDateString() }} s/d {{ $end->toDateString() }}
                            <span class="text-muted fw-normal">({{ (int) $order->days }} hari)</span>
                        </div>
                    </div>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Total</div>
                        <div class="kv-value">Rp
                            {{ number_format((int) ($order->amount_total ?? ($order->package_price ?? 0)), 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Metode</div>
                        <div class="kv-value">{{ strtoupper(str_replace('_', ' ', $order->payment_method ?? '-')) }}</div>
                    </div>

                    <div class="kv-row d-flex align-items-start py-2">
                        <div class="kv-label">Status</div>
                        <div class="kv-value">
                            @php
                                $statusClass = match ($order->status) {
                                    'PAID' => 'bg-success',
                                    'PENDING' => 'bg-info',
                                    'FAILED' => 'bg-danger',
                                    'EXPIRED' => 'bg-secondary',
                                    default => 'bg-warning',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === TANGGAL LAYANAN === --}}
            @if (is_array($serviceDates) && count($serviceDates))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">Tanggal Layanan</h6>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseServiceDates">
                                Lihat Semua ({{ count($serviceDates) }})
                            </button>
                        </div>
                        <div id="collapseServiceDates" class="collapse mt-2">
                            <div class="small d-flex flex-wrap gap-2">
                                @foreach ($serviceDates as $d)
                                    <span style="background: #3B9797"
                                        class="badge text-white p-2 ">{{ \Carbon\Carbon::parse($d)->toDateString() }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- === RENCANA MENU (CARD GRID) === --}}
            @if (is_array($menus) && count($menus))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3 text-center">Rencana Menu</h6>

                        <div class="row g-2">
                            @foreach ($menus as $menu)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div
                                        class="border rounded-3 text-center p-2 small bg-light h-100 d-flex align-items-center justify-content-center">
                                        <span class="fw-semibold text-truncate w-100"
                                            title="{{ $menu }}">{{ $menu }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- === TOMBOL AKSI === --}}
            <div class="text-justify">
                <a href="{{ route('orders.create') }}" class="btn btn-outline-secondary me-2">Buat Order Baru</a>
                @if ($order->status === 'UNPAID' || $order->status === 'PENDING')
                    <button id="btnPay" class="btn btn-primary">Bayar Sekarang</button>
                @else
                    <a href="{{ route('orders.finish', $order) }}" class="btn btn-success">Lihat Halaman Selesai</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

        /* =========================
           Helpers
        ========================= */
        function isExpireFromSnap(result) {
            // Toleran ke berbagai properti dari Midtrans
            const t = (result?.transaction_status || result?.status || result?.status_message || '').toString()
            .toLowerCase();
            return t.includes('expire') || t.includes('expired');
        }

        function isExpireFromServer(data) {
            const t = (data?.transaction_status || data?.status || '').toString().toLowerCase();
            return t === 'expire' || t === 'expired';
        }

        async function handleExpired() {
            // UX kecil sebelum refresh
            alert('Tagihan pembayaran sudah kadaluarsa. Kami akan membuatkan tagihan baru.');
            // Reload halaman yang sama -> Controller pay() akan membuat attempt + token baru
            window.location.reload();
        }

        /* Kirim jejak callback Snap ke server (opsional) */
        async function postSnapResult(payload) {
            try {
                await fetch(@json(route('orders.snap_result', $order)), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                console.error('snapResult error', e);
            }
        }

        /* Minta server konfirmasi status terbaru (server-to-server) */
        async function confirmToServer(midtransOrderId) {
            try {
                const res = await fetch(@json(route('orders.confirm', $order)), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        midtrans_order_id: midtransOrderId
                    })
                });
                const data = await res.json().catch(() => ({
                    ok: false
                }));
                console.log("confirmToServer >", data);
                return data;
            } catch (e) {
                console.error('confirm error', e);
                return {
                    ok: false
                };
            }
        }

        /* =========================
           Event: klik Bayar
        ========================= */
        document.getElementById('btnPay')?.addEventListener('click', function() {
            if (!window.snap || typeof window.snap.pay !== 'function') {
                alert('Gateway pembayaran belum siap. Coba lagi sebentar.');
                return;
            }

            window.snap.pay(@json($snapToken), {
                onSuccess: async (result) => {
                    // Berhasil
                    console.log("isi result >", result);
                    
                    await postSnapResult({
                        hook: 'success',
                        result
                    });
                    await confirmToServer(result?.order_id);
                    window.location.href = @json(route('orders.finish', $order));
                },

                onPending: async (result) => {
                    // Pending (bisa jadi nantinya expire). Kita tetap sync dulu.
                    console.log("isi result >", result);
                    await postSnapResult({
                        hook: 'pending',
                        result
                    });

                    // Cek cepat dari payload Snap
                    if (isExpireFromSnap(result)) {
                        console.log("isi result >", result);
                        return handleExpired();
                    }

                    // Verifikasi ke server (bisa return "expire" karena VA/Qris kadaluarsa)
                    const data = await confirmToServer(result?.order_id);
                    if (isExpireFromServer(data)) {
                        return handleExpired();
                    }

                    alert('Transaksi pending. Silakan selesaikan pembayaran Anda.');
                },

                onError: async (result) => {
                    // Error dari Snap (termasuk kemungkinan expired)
                    console.log("isi result >", result);
                    await postSnapResult({
                        hook: 'error',
                        result
                    });

                    if (isExpireFromSnap(result)) {
                        return handleExpired();
                    }

                    // Last check via server (kadang error generic tapi server sudah tahu itu expire)
                    const data = await confirmToServer(result?.order_id);
                    if (isExpireFromServer(data)) {
                        return handleExpired();
                    }

                    alert('Terjadi kesalahan saat memproses pembayaran.');
                },

                onClose: async () => {
                    // User nutup popup
                    await postSnapResult({
                        hook: 'close'
                    });
                    // (opsional) bisa lakukan apa-apa di sini; umumnya tidak direfresh
                }
            });
        });
    </script>
@endpush
