@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div iv class="container-xxl  container-p-y">
        <div class="row row-cols-1 row-cols-md-2 g-3 mt-1 align-items-stretch">

            {{-- KPI: User Aktif (2 card di satu kolom) --}}
            <div class="col d-flex flex-column gap-3">
                {{-- Customers Aktif --}}
                <div class="card border-0 shadow-sm h-100 flex-fill">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-secondary small">Customers Aktif</div>
                                <div class="h3 mb-0">{{ number_format($activeUserCount ?? 0) }}</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;background:#e9f7ef;">
                                <i class="bx bx-user-check" style="font-size:24px;color:#198754;"></i>
                            </div>
                        </div>

                        <div class="mt-2 small text-secondary">
                            Terakhir diperbarui: {{ now()->format('Y-m-d') }}
                        </div>

                        <div class="mt-3">
                            {{-- <a href="" class="btn btn-sm btn-outline-success w-100">
                                Lihat Daftar User
                            </a> --}}
                        </div>
                    </div>
                </div>

                {{-- Customers Tidak Aktif --}}
                <div class="card border-0 shadow-sm h-100 flex-fill">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-secondary small">Customers Tidak Aktif</div>
                                <div class="h3 mb-0">{{ number_format($inactiveUserCount ?? 0) }}</div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;background:#fdecea;">
                                <i class="bx bx-user-x" style="font-size:24px;color:#dc3545;"></i>
                            </div>
                        </div>

                        <div class="mt-2 small text-secondary">
                            Terakhir diperbarui: {{ now()->format('Y-m-d') }}
                        </div>

                        <div class="mt-3">
                            {{-- <a href="" class="btn btn-sm btn-outline-danger w-100">
                                Lihat Detail
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>


            {{-- Order Statistics --}}
            <div class="col d-flex">
                <div class="card h-100 w-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Order Statistics</h5>
                            <small class="text-muted">42.82k Total Sales</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <h2 class="mb-2">8,258</h2>
                                <span>Total Orders</span>
                            </div>
                            <div id="orderStatisticsChart"></div>
                        </div>
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-mobile-alt"></i>
                                    </span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Premium</h6>
                                        <small class="text-muted">paket 1, paket 2, paket 3</small>
                                    </div>
                                    <div class="user-progress">
                                        <small class="fw-semibold">82.5k</small>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-closet"></i>
                                    </span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Reguler</h6>
                                        <small class="text-muted">paket 1, paket 2, paket 3</small>
                                    </div>
                                    <div class="user-progress">
                                        <small class="fw-semibold">23.8k</small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>



        {{-- =========================================
        FILTER TANGGAL (Bootstrap only)
        ========================================= --}}
        <div class="mb-3 border-bottom">
            <form method="GET" class="py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label for="date" class="form-label mb-0">Tanggal</label>
                        <input type="date" id="date" name="date" class="form-control"
                            value="{{ $date }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Tampilkan</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">Hari Ini</a>
                    </div>
                </div>
            </form>
        </div>

        @php
            // ===== Helper untuk seluruh halaman =====
            // Badge status
            $badge = fn($s) => match ($s) {
                'pending' => 'bg-secondary',
                'diproses' => 'bg-info',
                'sedang dikirim' => 'bg-warning text-dark',
                'sampai' => 'bg-success',
                'gagal dikirim' => 'bg-danger',
                default => 'bg-secondary',
            };
            // Step untuk progress (1..4) atau -1 jika gagal
            $mapStep = fn($s) => match (Str::lower(trim($s ?? ''))) {
                'pending' => 1,
                'diproses' => 2,
                'sedang dikirim' => 3,
                'sampai' => 4,
                'gagal dikirim' => -1,
                default => 1,
            };

            // Render progress 4 segmen
            $renderStepper = function ($step) {
                // mapping warna berdasarkan step
                $color = match ($step) {
                    1 => 'bg-secondary',
                    2 => 'bg-warning',
                    3 => 'bg-info',
                    4 => 'bg-success',
                    -1 => 'bg-danger',
                    default => 'bg-secondary',
                };

                // mapping persentase progress
                $progress = match ($step) {
                    1 => 25,
                    2 => 50,
                    3 => 75,
                    4, -1 => 100,
                    default => 25,
                };

                // render HTML progress bar
                return '
      <div class="progress" role="progressbar" aria-label="Stepper" style="height: 8px;">
        <div class="progress-bar ' .
                    $color .
                    '" style="width:' .
                    $progress .
                    '%">
        </div>
      </div>
    ';
            };
        @endphp

        {{-- =========================================
        ROW 3: LIST PENGANTARAN (card per item)
        ========================================= --}}
        <div class="row g-3">
            @forelse ($items as $row)
                @php
                    $spec = $row->menuMakanan->spec_menu ?? [];
                    $menuSiang = $spec['Makan Siang'] ?? [];
                    $menuMalam = $spec['Makan Malam'] ?? [];
                    $stepSiang = $mapStep($row->status_siang);
                    $stepMalam = $mapStep($row->status_malam);
                @endphp

                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100">
                        {{-- HEADER --}}
                        <div class="card-header bg-light">
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h5 class="mb-0">{{ $row->mealPackage->nama_meal_package }}</h5>
                                    <span
                                        class="badge rounded-pill text-bg-info">{{ ucfirst($row->mealPackage->jenis_paket) }}</span>
                                    <span class="badge rounded-pill text-dark border">Batch {{ $row->batch }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-3 small text-secondary">
                                    <span><i class="bx bx-calendar"></i>
                                        {{ \Carbon\Carbon::parse($row->delivery_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                    </span>
                                    @if ($row->confirmed_by)
                                        <span><i class="bx bx-check-shield"></i>
                                            Disetujui:
                                            <strong>{{ $row->confirmedBy?->name ?? 'User #' . $row->confirmed_by }}</strong>
                                            ({{ \Carbon\Carbon::parse($row->confirmed_at)->format('Y-m-d H:i') }})
                                        </span>
                                    @else
                                        <span><i class="bx bx-time"></i> Belum dikonfirmasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- MENU PREVIEW --}}
                                <div class="col-lg-5">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="fw-semibold mb-1">Menu Siang</div>
                                            <ul class="list-unstyled small mb-0">
                                                @forelse(array_slice($menuSiang,0,4) as $m)
                                                    <li>• {{ $m }}</li>
                                                @empty
                                                    <li class="text-secondary">-</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 mt-3 mt-sm-0">
                                            <div class="fw-semibold mb-1">Menu Malam</div>
                                            <ul class="list-unstyled small mb-0">
                                                @forelse(array_slice($menuMalam,0,4) as $m)
                                                    <li>• {{ $m }}</li>
                                                @empty
                                                    <li class="text-secondary">-</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- STATUS SIANG --}}
                                <div class="col-lg-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-semibold">Status Siang</span>
                                                <span
                                                    class="badge {{ $badge($row->status_siang) }}">{{ strtoupper($row->status_siang) }}</span>
                                            </div>
                                            {!! $renderStepper($stepSiang) !!}
                                            <form method="POST" class="d-grid gap-2 mt-2"
                                                action="{{ route('admin.deliveries.updateStatus', $row->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="field" value="status_siang">

                                                <button type="submit"
                                                    class="btn btn-outline-secondary {{ $row->status_siang === 'pending' ? 'active' : '' }}"
                                                    name="value" value="pending"
                                                    {{ $row->status_siang === 'pending' ? 'disabled' : '' }}>
                                                    Pending
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-warning {{ $row->status_siang === 'diproses' ? 'active' : '' }}"
                                                    name="value" value="diproses"
                                                    {{ $row->status_siang === 'diproses' ? 'disabled' : '' }}>
                                                    Diproses
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-info {{ $row->status_siang === 'sedang dikirim' ? 'active' : '' }}"
                                                    name="value" value="sedang dikirim"
                                                    {{ $row->status_siang === 'sedang dikirim' ? 'disabled' : '' }}>
                                                    Sedang dikirim
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-success {{ $row->status_siang === 'sampai' ? 'active' : '' }}"
                                                    name="value" value="sampai"
                                                    {{ $row->status_siang === 'sampai' ? 'disabled' : '' }}>
                                                    Sampai
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-danger {{ $row->status_siang === 'gagal dikirim' ? 'active' : '' }}"
                                                    name="value" value="gagal dikirim"
                                                    {{ $row->status_siang === 'gagal dikirim' ? 'disabled' : '' }}>
                                                    Gagal DIkirim
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                {{-- STATUS MALAM --}}
                                <div class="col-lg-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-semibold">Status Malam</span>
                                                <span
                                                    class="badge {{ $badge($row->status_malam) }}">{{ strtoupper($row->status_malam) }}</span>
                                            </div>
                                            {!! $renderStepper($stepMalam) !!}
                                            <form method="POST" class="d-grid gap-2 mt-2"
                                                action="{{ route('admin.deliveries.updateStatus', $row->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="field" value="status_malam">

                                                <button type="submit"
                                                    class="btn btn-outline-secondary {{ $row->status_malam === 'pending' ? 'active' : '' }}"
                                                    name="value" value="pending"
                                                    {{ $row->status_malam === 'pending' ? 'disabled' : '' }}>
                                                    Pending
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-warning {{ $row->status_malam === 'diproses' ? 'active' : '' }}"
                                                    name="value" value="diproses"
                                                    {{ $row->status_malam === 'diproses' ? 'disabled' : '' }}>
                                                    Diproses
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-info {{ $row->status_malam === 'sedang dikirim' ? 'active' : '' }}"
                                                    name="value" value="sedang dikirim"
                                                    {{ $row->status_malam === 'sedang dikirim' ? 'disabled' : '' }}>
                                                    Sedang dikirim
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-success {{ $row->status_malam === 'sampai' ? 'active' : '' }}"
                                                    name="value" value="sampai"
                                                    {{ $row->status_malam === 'sampai' ? 'disabled' : '' }}>
                                                    Sampai
                                                </button>

                                                <button type="submit"
                                                    class="btn btn-outline-danger {{ $row->status_malam === 'gagal dikirim' ? 'active' : '' }}"
                                                    name="value" value="gagal dikirim"
                                                    {{ $row->status_malam === 'gagal dikirim' ? 'disabled' : '' }}>
                                                    Gagal DIkirim
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
       ROW 4: SLOT CARD LAIN (tinggal tambah)
     ========================================= --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mt-1">
            {{-- Contoh placeholder card quick actions --}}
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Aksi Cepat</h6>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        <a href="" class="btn btn-primary btn-sm">Buat Pesanan</a>
                        <a href="" class="btn btn-outline-secondary btn-sm">Lihat Issue</a>
                        <a href="" class="btn btn-outline-primary btn-sm">Report</a>
                    </div>
                </div>
            </div>

            {{-- Tambah card lain di sini --}}
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">Card kosong (isi nanti)</div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">Card kosong (isi nanti)</div>
                </div>
            </div>
        </div>


    </div>
@endsection
