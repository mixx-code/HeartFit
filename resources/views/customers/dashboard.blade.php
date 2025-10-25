@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-4">

@php
  use Illuminate\Support\Str;
  $row = $items->first();
@endphp

{{-- === STATUS PENGANTARAN (KONDISIONAL) === --}}
@if(!$row)
  <div class="col-12">
    <div class="alert alert-warning">Belum ada pengantaran untuk hari ini.</div>
  </div>
@else
  @php
    $indoDate = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y');

    // MENU
    $spec = $row?->menuMakanan->spec_menu ?? [];
    $menuSiang = $spec['Makan Siang'] ?? [];
    $menuMalam = $spec['Makan Malam'] ?? [];

    // === 4 LANGKAH ===
    $steps = ['PENDING','DIPROSES','SEDANG DIKIRIM','SAMPAI'];

    // mapping status -> posisi langkah (1..4)
    $mapStep = fn($s) => match(Str::lower(trim($s ?? ''))) {
        'pending'         => 1,
        'diproses'        => 2,
        'sedang dikirim'  => 3,
        'sampai'          => 4,
        'gagal dikirim'   => 3, // gagal di fase kirim
        default           => 1,
    };

    $stepSiang = $mapStep($row->status_siang);
    $stepMalam = $mapStep($row->status_malam);

    $renderStepper = function (int $currentStep, bool $isFailed = false) use ($steps) {
        $total = count($steps);
        $segments = max(1, $total - 1);
        $isAllDone = (!$isFailed && $currentStep >= $total);

        if ($isAllDone) {
            $progressPct = 100;
        } else {
            $progressSeg = $isFailed ? max(0, $currentStep - 2) : max(0, $currentStep - 1);
            $progressPct = ($progressSeg / $segments) * 100;
        }

        $labels = $steps;
        if ($isFailed && $currentStep >= 1 && $currentStep <= $total) {
            $labels[$currentStep - 1] = 'GAGAL DIKIRIM';
        }
  @endphp
      <div class="position-relative bg-white rounded-3 border px-3 py-4">
    {{-- baseline abu --}}
    <div class="position-absolute start-0 end-0" style="top:37%;height:3px;background:#e5e7eb;z-index:0"></div>
    {{-- progress: merah saat gagal, hijau normal/selesai --}}
    <div class="position-absolute start-0"
         style="top:37%;height:3px;width:{{ $progressPct }}%;
                background:{{ $isFailed ? '#CD2C58' : '#28a745' }};z-index:0;"></div>

    <div class="row gx-0 text-center">
      @for ($i = 1; $i <= $total; $i++)
        @php
          $failedNow     =  $isFailed && $i === $currentStep;     // titik gagal
          $failedTrail   =  $isFailed && $i <  $currentStep;       // titik sebelum gagal (ikut merah)
          $isActive      = !$isFailed && !$isAllDone && ($i === $currentStep); // aktif normal -> jam
          $isCompleted   = !$isFailed && ($isAllDone ? true : ($i < $currentStep)); // selesai normal/hijau
          $isPendingIdx  = ($i === 1);
        @endphp

        <div class="col d-flex flex-column align-items-center"
             style="flex:0 0 calc(100%/{{ $total }});max-width:calc(100%/{{ $total }});">

          @php
            // Lingkaran:
            // - gagal (current & trail) => merah
            // - aktif normal => kuning
            // - selesai normal => hijau
            // - belum mulai => abu
            $circleClass =
              ($failedNow || $failedTrail) ? 'border-danger bg-danger text-white' :
              ($isActive && $isPendingIdx ? 'border-warning bg-warning text-white' :
              ($isActive ? 'border-warning bg-warning text-white' :
              ($isCompleted ? 'border-success bg-success text-white' :
               'bg-white border-secondary text-secondary')));

            // Label:
            // - gagal (current & trail) => merah
            // - aktif normal => kuning
            // - selesai normal => hijau
            // - belum mulai => abu
            $labelClass =
              ($failedNow || $failedTrail) ? 'text-danger fw-semibold' :
              ($isActive && $isPendingIdx ? 'text-warning fw-semibold' :
              ($isActive ? 'text-warning fw-semibold' :
              ($isCompleted ? 'text-success fw-semibold' : 'text-secondary')));
          @endphp

          <div class="rounded-circle d-flex align-items-center justify-content-center {{ $circleClass }}"
               style="width:44px;height:44px;margin-top:10px;z-index:1;">
            @if($failedNow)
              {{-- titik gagal --}}
              <span class="fw-bold"><i class="bx bx-x fs-5"></i></span>
            @elseif($failedTrail)
              {{-- titik sebelum gagal: tetap centang tapi merah --}}
              <span class="fw-bold"><i class="bx bx-x fs-5"></i></span>
            @elseif($isActive)
              {{-- step aktif normal --}}
              <i class="bx bx-time-five fs-5"></i>
            @elseif($isCompleted)
              {{-- selesai normal --}}
              <span class="fw-bold">✓</span>
            @else
              <span class="fw-bold opacity-50">•</span>
            @endif
          </div>

          <small class="mt-2 {{ $labelClass }}">
            {{ $labels[$i-1] }}
          </small>
        </div>
      @endfor
    </div>
  </div>
  @php
    }; // end renderStepper
  @endphp

  {{-- === CARD SIANG === --}}
  @php $isFailedSiang = Str::lower($row->status_siang) === 'gagal dikirim'; @endphp
  <div class="col-md-6 col-lg-4">
    <div class="card shadow border-0 h-100 overflow-hidden">
      <div class="card-header fw-semibold text-white" style="background-color:#5DD64C;">
        Status Pengantaran Untuk Siang Ini
      </div>
      <div class="card-body bg-light mt-2">
        <div class="d-flex justify-content-between small text-secondary mb-1">
          <span>TANGGAL</span>
          <span class="fw-semibold text-dark">{{ $indoDate }}</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-1">
          <span>BATCH</span>
          <span class="fw-semibold text-dark">Batch {{ $row->batch }}</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-3">
          <span>PAKET</span>
          <span class="fw-semibold text-dark">{{ $row->mealPackage->nama_meal_package ?? 'Paket #'.$row->meal_package_id }}</span>
        </div>

        {!! $renderStepper($stepSiang, $isFailedSiang) !!}

        @if(!empty($menuSiang))
          <div class="mt-4">
            <h6 class="text-success fw-bold mb-2">Menu Siang</h6>
            <ul class="list-unstyled mb-0">
              @foreach($menuSiang as $m)
                <li class="mb-1"><i class="bx bx-dish text-success me-2"></i>{{ $m }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- === CARD MALAM === --}}
  @php $isFailedMalam = Str::lower($row->status_malam) === 'gagal dikirim'; @endphp
  <div class="col-md-6 col-lg-4">
    <div class="card shadow border-0 h-100 overflow-hidden">
      <div class="card-header fw-semibold text-white" style="background-color:#5DD64C;">
        Status Pengantaran Untuk Malam Ini
      </div>
      <div class="card-body bg-light mt-2">
        <div class="d-flex justify-content-between small text-secondary mb-1">
          <span>TANGGAL</span>
          <span class="fw-semibold text-dark">{{ $indoDate }}</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-1">
          <span>BATCH</span>
          <span class="fw-semibold text-dark">Batch {{ $row->batch }}</span>
        </div>
        <div class="d-flex justify-content-between small text-secondary mb-3">
          <span>PAKET</span>
          <span class="fw-semibold text-dark">{{ $row->mealPackage->nama_meal_package ?? 'Paket #'.$row->meal_package_id }}</span>
        </div>

        {!! $renderStepper($stepMalam, $isFailedMalam) !!}

        @if(!empty($menuMalam))
          <div class="mt-4">
            <h6 class="text-success fw-bold mb-2">Menu Malam</h6>
            <ul class="list-unstyled mb-0">
              @foreach($menuMalam as $m)
                <li class="mb-1"><i class="bx bx-dish text-success me-2"></i>{{ $m }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>
  </div>
@endif

      {{-- CTA PESAN (SELALU TAMPIL) --}}
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h5 class="card-title mb-2">Pesan HeartFit dengan Mudah</h5>
            <p class="card-text text-secondary mb-3">
              Ingin mulai langganan makan sehat? <strong>Kalau mau pesan, klik di sini</strong> untuk memilih
              paket dan atur jadwal pengantaran sesuai kebutuhanmu.
            </p>
            @if (session('warning'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
      {{ session('warning') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
            <a href="{{ route('orders.create') }}" class="btn btn-primary">Pesan Sekarang</a>
          </div>
        </div>
      </div>

            {{-- KONTEN PAKET --}}
            <div id="paket" class="col-12">
                <section class="py-5 bg-white border rounded-3">
                    <div class="container">
                        {{-- Header Reguler --}}
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary mb-1">HeartFit Diet Reguler</h3>
                            <p class="text-secondary mb-0">Pilihan paket makan sehat untuk kebutuhan harian Anda.</p>
                        </div>

                        {{-- === Card Paket Reguler === --}}
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-lg h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="text-uppercase text-secondary mb-1">Reguler</h6>
                                        <div class="h3 fw-bold mt-1">Rp 50.000,-</div>
                                        <ul class="list-group list-group-flush mt-3">
                                            <li class="list-group-item">Menu seimbang</li>
                                            <li class="list-group-item">Pilihan karbo sehat</li>
                                        </ul>
                                        <a href="" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-0 shadow-lg h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="text-uppercase text-secondary mb-1">Mingguan</h6>
                                        <div class="h3 fw-bold mt-1">Rp 400.000,-</div>
                                        <ul class="list-group list-group-flush mt-3">
                                            <li class="list-group-item">4 hari × 2 kali makan</li>
                                            <li class="list-group-item">8 hari × 1 kali makan</li>
                                        </ul>
                                        <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-0 shadow-lg h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="text-uppercase text-secondary mb-1">Bulanan</h6>
                                        <div class="h3 fw-bold mt-1">Rp 1.180.000,-</div>
                                        <ul class="list-group list-group-flush mt-3">
                                            <li class="list-group-item">12 hari × 2 kali makan</li>
                                            <li class="list-group-item">24 hari × 1 kali makan</li>
                                        </ul>
                                        <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-0 shadow-lg h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="text-uppercase text-secondary mb-1">3 Bulanan</h6>
                                        <div class="h3 fw-bold mt-1">Rp 3.540.000,-</div>
                                        <ul class="list-group list-group-flush mt-3">
                                            <li class="list-group-item">36 hari × 2 kali makan</li>
                                            <li class="list-group-item">72 hari × 1 kali makan</li>
                                        </ul>
                                        <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ... (lanjutan Premium dan kalender tetap sama seperti sebelumnya) ... --}}
                    </div>
                </section>
            </div>

            {{-- Kalender --}}
            <div class="col-12">
                <div class="py-5 bg-white border rounded-3">
                    @include('customers.kalender')
                </div>
            </div>

        </div>
    </div>
@endsection
