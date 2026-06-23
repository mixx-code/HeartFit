@extends('layouts.app')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- =========================================
         SECTION 1: DELIVERY (view-only)
    ========================================= --}}
    <div class="mb-3 border-bottom">
        <div class="py-2 d-flex flex-wrap align-items-end gap-3">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label for="date" class="form-label mb-0">Tanggal</label>
                    <input type="date" id="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <button class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('dashboard.superadmin') }}" class="btn btn-outline-secondary">Hari Ini</a>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bx bx-x-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $badge = fn($s) => match ($s) {
            'pending'        => 'bg-secondary',
            'diproses'       => 'bg-info',
            'sedang dikirim' => 'bg-warning text-dark',
            'sampai'         => 'bg-success',
            'gagal dikirim'  => 'bg-danger',
            default          => 'bg-secondary',
        };
    @endphp

    {{-- =========================================
         TABEL PENGANTARAN (view-only)
    ========================================= --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light d-flex align-items-center gap-2">
            <i class="bx bx-package fs-5 text-primary"></i>
            <span class="fw-semibold">Pengantaran</span>
            <span class="badge bg-secondary ms-1">{{ $items->count() }}</span>
            <small class="text-muted ms-auto">
                <i class="bx bx-calendar me-1"></i>
                {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:42px">#</th>
                            <th>Paket</th>
                            <th>Batch</th>
                            <th class="text-center">Siang</th>
                            <th class="text-center">Malam</th>
                            <th>Konfirmasi</th>
                            <th class="pe-3" style="width:90px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $row)
                            @php
                                $spec      = $row->menuMakanan->spec_menu ?? [];
                                $menuSiang = array_slice($spec['Makan Siang'] ?? [], 0, 10);
                                $menuMalam = array_slice($spec['Makan Malam'] ?? [], 0, 10);
                            @endphp
                            <tr>
                                <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold lh-sm">{{ $row->mealPackage->nama_meal_package }}</div>
                                    <span class="badge text-bg-info" style="font-size:10px">{{ ucfirst($row->mealPackage->jenis_paket) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">Batch {{ $row->batch }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badge($row->status_siang) }}">{{ ucfirst($row->status_siang) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badge($row->status_malam) }}">{{ ucfirst($row->status_malam) }}</span>
                                </td>
                                <td class="small">
                                    @if ($row->confirmed_by)
                                        <i class="bx bx-check-shield text-success"></i>
                                        {{ $row->confirmer?->name ?? '-' }}
                                    @else
                                        <span class="text-warning">
                                            <i class="bx bx-time"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-3">
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#deliveryOffcanvasSA"
                                        data-paket="{{ $row->mealPackage->nama_meal_package }}"
                                        data-jenis="{{ ucfirst($row->mealPackage->jenis_paket) }}"
                                        data-batch="{{ $row->batch }}"
                                        data-date="{{ \Carbon\Carbon::parse($row->delivery_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}"
                                        data-status-siang="{{ $row->status_siang }}"
                                        data-status-malam="{{ $row->status_malam }}"
                                        data-confirmed="{{ $row->confirmed_by ? ($row->confirmer?->name ?? '-') : '' }}"
                                        data-confirmed-at="{{ $row->confirmed_at ? \Carbon\Carbon::parse($row->confirmed_at)->format('d/m/Y H:i') : '' }}"
                                        data-menu-siang="{{ json_encode($menuSiang) }}"
                                        data-menu-malam="{{ json_encode($menuMalam) }}">
                                        <i class="bx bx-detail"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bx bx-inbox fs-2 d-block mb-1"></i>
                                    Belum ada pengantaran untuk tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================================
         SECTION 2: RIWAYAT PENGANTARAN
    ========================================= --}}
    @if($history->count() > 0)
    <div class="card shadow border-0 mb-4">
        <div class="card-header fw-semibold text-white d-flex align-items-center gap-2" style="background-color:#5DD64C;">
            <i class="bx bx-history fs-5"></i> Riwayat Pengantaran
            <span class="badge bg-white text-dark ms-auto">{{ $history->count() }} data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Paket</th>
                            <th>Menu</th>
                            <th>Batch</th>
                            <th class="text-center">Status Siang</th>
                            <th class="text-center">Status Malam</th>
                            <th>Dikonfirmasi</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat-tbody-superadmin">
                        @foreach($history as $h)
                        @php
                            $hBadge = fn($s) => match(strtolower($s ?? '')) {
                                'sampai'         => 'success',
                                'gagal dikirim'  => 'danger',
                                'sedang dikirim' => 'warning text-dark',
                                'diproses'       => 'info',
                                default          => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 fw-semibold">
                                {{ \Carbon\Carbon::parse($h->delivery_date)->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td>{{ $h->mealPackage->nama_meal_package ?? '-' }}</td>
                            <td>{{ $h->menuMakanan->nama_menu ?? '-' }}</td>
                            <td><span class="badge bg-label-secondary">{{ $h->batch }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-{{ $hBadge($h->status_siang) }}">{{ ucfirst($h->status_siang) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $hBadge($h->status_malam) }}">{{ ucfirst($h->status_malam) }}</span>
                            </td>
                            <td class="small text-muted">
                                @if($h->confirmer)
                                    <i class="bx bx-check-shield text-success me-1"></i>{{ $h->confirmer->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top" id="riwayat-pagination-superadmin">
                <small class="text-muted" id="riwayat-info-superadmin"></small>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="riwayat-prev-superadmin" onclick="riwayatPage('superadmin',-1)">&#8592; Sebelumnya</button>
                    <button class="btn btn-sm btn-outline-secondary" id="riwayat-next-superadmin" onclick="riwayatPage('superadmin',1)">Selanjutnya &#8594;</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- =========================================
         SECTION 3: AHLI GIZI (summary + orders)
    ========================================= --}}
    <hr class="my-4">
    <h5 class="mb-3">Ringkasan & Data Order</h5>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Total Customer</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['total_customers']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Total Orders</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['total_orders']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-receipt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Order Aktif Hari Ini</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['active_today']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Orders Bulan Ini</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['orders_this_month']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Order Customer - Paket Personal</h5>
            <small class="text-muted float-end">Daftar order customer paket personal</small>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 mb-3">
                <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('dashboard.superadmin') }}">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div class="input-group" style="min-width: 280px;">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari order...">
                    </div>
                    <div class="input-group" style="max-width: 160px;">
                        <span class="input-group-text">Rows</span>
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            @foreach ([5, 10, 15, 20] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', $perPage) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if (request('q'))
                        <a href="{{ route('dashboard.superadmin', ['date' => $date, 'per_page' => request('per_page', $perPage)]) }}"
                           class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive text-nowrap" style="min-height: 400px">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No. Order</th>
                            <th>Nama Penerima</th>
                            <th>Email</th>
                            <th>Paket</th>
                            <th>Nomor WA</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td><span class="fw-semibold">{{ $order->user->name }}</span></td>
                                <td>{{ $order->user->email }}</td>
                                <td>{{ $order->package_label }}</td>
                                <td>
                                    @if($order->user->detail && $order->user->detail->hp)
                                        <span class="badge bg-label-success">{{ $order->user->detail->hp }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-detail me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Tidak ada data order paket personal{{ request('q') ? ' untuk pencarian ini' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="small text-muted">
                    @if($orders->total() > 0)
                        Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} data
                    @else
                        Menampilkan 0–0 dari 0 data
                    @endif
                </div>
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
            </div>
        </div>
    </div>

</div>

    {{-- =========================================
         OFFCANVAS DETAIL PENGANTARAN (view-only)
    ========================================= --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="deliveryOffcanvasSA" style="width:420px">
        <div class="offcanvas-header border-bottom">
            <div class="overflow-hidden pe-2">
                <h6 class="offcanvas-title fw-semibold mb-1 text-truncate" id="sa-oc-paket">-</h6>
                <div class="small d-flex flex-wrap align-items-center gap-1" id="sa-oc-meta"></div>
            </div>
            <button type="button" class="btn-close flex-shrink-0 ms-2" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">

            {{-- Konfirmasi --}}
            <div class="mb-3 small" id="sa-oc-confirmed"></div>

            {{-- Menu --}}
            <div class="row g-2 mb-4 pb-3 border-bottom">
                <div class="col-6">
                    <div class="fw-semibold small mb-2">Menu Siang</div>
                    <ul class="list-unstyled small mb-0" id="sa-oc-menu-siang"></ul>
                </div>
                <div class="col-6">
                    <div class="fw-semibold small mb-2">Menu Malam</div>
                    <ul class="list-unstyled small mb-0" id="sa-oc-menu-malam"></ul>
                </div>
            </div>

            {{-- Status Siang (view-only) --}}
            <div class="mb-4 pb-3 border-bottom">
                <div class="fw-semibold small mb-2">Status Siang</div>
                <div class="mb-2" id="sa-oc-badge-siang"></div>
                <div id="sa-oc-stepper-siang"></div>
            </div>

            {{-- Status Malam (view-only) --}}
            <div>
                <div class="fw-semibold small mb-2">Status Malam</div>
                <div class="mb-2" id="sa-oc-badge-malam"></div>
                <div id="sa-oc-stepper-malam"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    // ── Riwayat pagination ──────────────────────────────────────────────
    const _riwayatState = {};
    function initRiwayat(key, perPage) {
        const tbody = document.getElementById('riwayat-tbody-' + key);
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const totalPages = Math.ceil(rows.length / perPage);
        _riwayatState[key] = { rows, perPage, page: 1, totalPages };
        if (totalPages <= 1) {
            document.getElementById('riwayat-pagination-' + key)?.classList.add('d-none');
            return;
        }
        renderRiwayat(key);
    }
    function renderRiwayat(key) {
        const { rows, perPage, page, totalPages } = _riwayatState[key];
        const start = (page - 1) * perPage;
        rows.forEach((r, i) => r.style.display = (i >= start && i < start + perPage) ? '' : 'none');
        document.getElementById('riwayat-info-' + key).textContent = `Halaman ${page} dari ${totalPages}`;
        document.getElementById('riwayat-prev-' + key).disabled = page <= 1;
        document.getElementById('riwayat-next-' + key).disabled = page >= totalPages;
    }
    function riwayatPage(key, dir) {
        const s = _riwayatState[key];
        s.page = Math.min(Math.max(s.page + dir, 1), s.totalPages);
        renderRiwayat(key);
    }

    // ── Offcanvas detail (view-only) ────────────────────────────────────
    const BADGE_CLS = {
        'pending':        'bg-secondary',
        'diproses':       'bg-info',
        'sedang dikirim': 'bg-warning text-dark',
        'sampai':         'bg-success',
        'gagal dikirim':  'bg-danger',
    };
    const STEP_CFG = {
        'pending':        { pct: 25,  cls: 'bg-secondary' },
        'diproses':       { pct: 50,  cls: 'bg-warning' },
        'sedang dikirim': { pct: 75,  cls: 'bg-info' },
        'sampai':         { pct: 100, cls: 'bg-success' },
        'gagal dikirim':  { pct: 100, cls: 'bg-danger' },
    };

    function buildStepper(status) {
        const { pct, cls } = STEP_CFG[status] ?? STEP_CFG['pending'];
        return `<div class="progress" style="height:8px">
                    <div class="progress-bar ${cls}" style="width:${pct}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1" style="font-size:10px;color:#aaa">
                    <span>Pending</span><span>Diproses</span><span>Dikirim</span><span>Sampai</span>
                </div>`;
    }

    function buildMenuList(items) {
        if (!items || !items.length) return '<li class="text-muted">-</li>';
        return items.map(m => `<li class="py-1 border-bottom">${m}</li>`).join('');
    }

    document.addEventListener('DOMContentLoaded', function () {
        initRiwayat('superadmin', 5);

        const ocEl = document.getElementById('deliveryOffcanvasSA');
        if (!ocEl) return;

        ocEl.addEventListener('show.bs.offcanvas', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const d = btn.dataset;

            document.getElementById('sa-oc-paket').textContent = d.paket;
            document.getElementById('sa-oc-meta').innerHTML =
                `<span class="badge text-bg-info">${d.jenis}</span>` +
                `<span class="badge text-bg-secondary">Batch ${d.batch}</span>` +
                `<span class="text-muted"><i class="bx bx-calendar me-1"></i>${d.date}</span>`;

            const confEl = document.getElementById('sa-oc-confirmed');
            confEl.innerHTML = d.confirmed
                ? `<i class="bx bx-check-shield text-success me-1"></i>${d.confirmed} &mdash; ${d.confirmedAt}`
                : `<i class="bx bx-time text-warning me-1"></i><span class="text-muted">Belum dikonfirmasi</span>`;

            document.getElementById('sa-oc-menu-siang').innerHTML = buildMenuList(JSON.parse(d.menuSiang || '[]'));
            document.getElementById('sa-oc-menu-malam').innerHTML = buildMenuList(JSON.parse(d.menuMalam || '[]'));

            ['siang', 'malam'].forEach(slot => {
                const key = 'status' + slot.charAt(0).toUpperCase() + slot.slice(1);
                const status = d[key] ?? 'pending';
                document.getElementById(`sa-oc-badge-${slot}`).innerHTML =
                    `<span class="badge ${BADGE_CLS[status] ?? 'bg-secondary'}">${status.toUpperCase()}</span>`;
                document.getElementById(`sa-oc-stepper-${slot}`).innerHTML = buildStepper(status);
            });
        });
    });
</script>
@endpush
