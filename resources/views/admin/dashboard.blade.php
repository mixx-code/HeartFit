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
    </div>
@endsection
