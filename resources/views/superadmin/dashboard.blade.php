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
        $mapStep = fn($s) => match (Str::lower(trim($s ?? ''))) {
            'pending'        => 1,
            'diproses'       => 2,
            'sedang dikirim' => 3,
            'sampai'         => 4,
            'gagal dikirim'  => -1,
            default          => 1,
        };
        $renderStepper = function ($step) {
            $color = match ($step) {
                1       => 'bg-secondary',
                2       => 'bg-warning',
                3       => 'bg-info',
                4       => 'bg-success',
                -1      => 'bg-danger',
                default => 'bg-secondary',
            };
            $progress = match ($step) {
                1       => 25,
                2       => 50,
                3       => 75,
                4, -1   => 100,
                default => 25,
            };
            return '<div class="progress" role="progressbar" style="height: 8px;">
                      <div class="progress-bar ' . $color . '" style="width:' . $progress . '%"></div>
                    </div>';
        };
    @endphp

    <div class="row g-3 mb-4">
        @forelse ($items as $row)
            @php
                $spec     = $row->menuMakanan->spec_menu ?? [];
                $menuSiang = $spec['Makan Siang'] ?? [];
                $menuMalam = $spec['Makan Malam'] ?? [];
                $stepSiang = $mapStep($row->status_siang);
                $stepMalam = $mapStep($row->status_malam);
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h6 class="mb-0 fw-semibold">{{ $row->mealPackage->nama_meal_package }}</h6>
                                    <span class="badge rounded-pill text-bg-info">{{ ucfirst($row->mealPackage->jenis_paket) }}</span>
                                    <span class="badge rounded-pill text-bg-secondary">Batch {{ $row->batch }}</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <i class="bx bx-calendar me-1"></i>{{ \Carbon\Carbon::parse($row->delivery_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </div>
                            </div>
                            <div class="small text-muted">
                                @if ($row->confirmed_by)
                                    <i class="bx bx-check-shield text-success me-1"></i>
                                    {{ $row->confirmer?->name ?? '-' }}
                                    &mdash; {{ \Carbon\Carbon::parse($row->confirmed_at)->format('d/m/Y H:i') }}
                                @else
                                    <i class="bx bx-time text-warning me-1"></i>Belum dikonfirmasi
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Menu Preview --}}
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small mb-2">Menu Siang</div>
                                        <ul class="list-unstyled small mb-0">
                                            @forelse(array_slice($menuSiang, 0, 5) as $m)
                                                <li class="py-1 border-bottom">{{ $m }}</li>
                                            @empty
                                                <li class="text-muted">-</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div class="col-sm-6 mt-3 mt-sm-0">
                                        <div class="fw-semibold small mb-2">Menu Malam</div>
                                        <ul class="list-unstyled small mb-0">
                                            @forelse(array_slice($menuMalam, 0, 5) as $m)
                                                <li class="py-1 border-bottom">{{ $m }}</li>
                                            @empty
                                                <li class="text-muted">-</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Siang (view-only) --}}
                            <div class="col-lg-3">
                                <div class="fw-semibold small mb-2">Status Siang</div>
                                <span class="badge {{ $badge($row->status_siang) }} mb-2">{{ strtoupper($row->status_siang) }}</span>
                                {!! $renderStepper($stepSiang) !!}
                            </div>

                            {{-- Status Malam (view-only) --}}
                            <div class="col-lg-3">
                                <div class="fw-semibold small mb-2">Status Malam</div>
                                <span class="badge {{ $badge($row->status_malam) }} mb-2">{{ strtoupper($row->status_malam) }}</span>
                                {!! $renderStepper($stepMalam) !!}
                            </div>

                            <div class="col-lg-1 d-none d-lg-block"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary">Belum ada pengantaran untuk tanggal ini.</div>
            </div>
        @endforelse
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
                    <tbody>
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
                            <th>Customer</th>
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
                                <td>{{ $order->user->name }}</td>
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
@endsection
