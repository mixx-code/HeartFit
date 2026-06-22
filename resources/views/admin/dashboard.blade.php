@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- =========================================
       FILTER TANGGAL (Bootstrap only)
     ========================================= --}}
        <div class="mb-3 border-bottom">
            <div class="py-2 d-flex flex-wrap align-items-end gap-3">
                {{-- Filter Tanggal --}}
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                    <div>
                        <label for="date" class="form-label mb-0">Tanggal</label>
                        <input type="date" id="date" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">Hari Ini</a>
                </form>

                {{-- Generate Delivery Manual — hanya role yang ada di settings.delivery.generate --}}
                @if(in_array(auth()->user()->role, config('settings.delivery.generate', [])))
                <form method="POST" action="{{ route('admin.deliveries.generate') }}" class="ms-auto"
                      onsubmit="return confirm('Generate delivery untuk tanggal {{ $date }}?')">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-refresh me-1"></i>Generate Delivery
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
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
                    '%"></div>
      </div>
    ';
            };
        @endphp
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
            {{-- <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Pesanan Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        <div class="display-6 fw-bold">{{ $kpi['orders_today'] ?? 0 }}</div>
                        <div class="small text-secondary mt-1">Termasuk reguler & premium</div>
                        @if (isset($kpi['orders_progress']))
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Target harian</span><span>{{ (int) $kpi['orders_progress'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ (int) $kpi['orders_progress'] }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Pengantaran Selesai</h6>
                    </div>
                    <div class="card-body">
                        <div class="display-6 fw-bold">{{ $kpi['delivered'] ?? 0 }}</div>
                        <div class="small text-secondary mt-1">Siang + Malam</div>
                        @if (isset($kpi['delivered_pct']))
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progres</span><span>{{ (int) $kpi['delivered_pct'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ (int) $kpi['delivered_pct'] }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Sedang Dikirim</h6>
                    </div>
                    <div class="card-body">
                        <div class="display-6 fw-bold">{{ $kpi['shipping'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Gagal Dikirim</h6>
                    </div>
                    <div class="card-body">
                        <div class="display-6 fw-bold">{{ $kpi['failed'] ?? 0 }}</div>
                        <div class="small text-secondary mt-1">Butuh follow-up</div>
                    </div>
                </div>
            </div> --}}
        </div>
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
                    <div class="card border-0 shadow-sm">
                        {{-- HEADER --}}
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

                        {{-- BODY --}}
                        <div class="card-body">
                            <div class="row g-3">

                                {{-- MENU PREVIEW --}}
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

                                {{-- STATUS SIANG --}}
                                <div class="col-lg-3">
                                    <div class="fw-semibold small mb-2">Status Siang</div>
                                    <span class="badge {{ $badge($row->status_siang) }} mb-2">{{ strtoupper($row->status_siang) }}</span>
                                    {!! $renderStepper($stepSiang) !!}
                                    <form method="POST" class="mt-3 d-flex gap-2 align-items-center"
                                        action="{{ route('admin.deliveries.updateStatus', $row->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="field" value="status_siang">
                                        <select name="value" class="form-select form-select-sm">
                                            @foreach(['pending','diproses','sedang dikirim','sampai','gagal dikirim'] as $s)
                                                <option value="{{ $s }}" {{ $row->status_siang === $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">submit</button>
                                    </form>
                                </div>

                                {{-- STATUS MALAM --}}
                                <div class="col-lg-3">
                                    <div class="fw-semibold small mb-2">Status Malam</div>
                                    <span class="badge {{ $badge($row->status_malam) }} mb-2">{{ strtoupper($row->status_malam) }}</span>
                                    {!! $renderStepper($stepMalam) !!}
                                    <form method="POST" class="mt-3 d-flex gap-2 align-items-center"
                                        action="{{ route('admin.deliveries.updateStatus', $row->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="field" value="status_malam">
                                        <select name="value" class="form-select form-select-sm">
                                            @foreach(['pending','diproses','sedang dikirim','sampai','gagal dikirim'] as $s)
                                                <option value="{{ $s }}" {{ $row->status_malam === $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">submit</button>
                                    </form>
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

        {{-- RIWAYAT PENGANTARAN --}}
        @if($history->count() > 0)
        <div class="card shadow border-0 mt-4">
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
                        <tbody id="riwayat-tbody-admin">
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
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top" id="riwayat-pagination-admin">
                    <small class="text-muted" id="riwayat-info-admin"></small>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" id="riwayat-prev-admin" onclick="riwayatPage('admin',-1)">&#8592; Sebelumnya</button>
                        <button class="btn btn-sm btn-outline-secondary" id="riwayat-next-admin" onclick="riwayatPage('admin',1)">Selanjutnya &#8594;</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
@endsection

@push('scripts')
<script>
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
    document.addEventListener('DOMContentLoaded', () => initRiwayat('admin', 5));
</script>
@endpush
