@php
  use Illuminate\Support\Str;
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

    // === 4 LANGKAH (bisa di-override dari props) ===
    $steps = $steps ?? ['PENDING','DIPROSES','SEDANG DIKIRIM','SAMPAI'];

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

              // Lingkaran
              $circleClass =
                ($failedNow || $failedTrail) ? 'border-danger bg-danger text-white' :
                ($isActive ? 'border-warning bg-warning text-white' :
                ($isCompleted ? 'border-success bg-success text-white' :
                 'bg-white border-secondary text-secondary'));

              // Label
              $labelClass =
                ($failedNow || $failedTrail) ? 'text-danger fw-semibold' :
                ($isActive ? 'text-warning fw-semibold' :
                ($isCompleted ? 'text-success fw-semibold' : 'text-secondary'));
            @endphp

            <div class="col d-flex flex-column align-items-center"
                 style="flex:0 0 calc(100%/{{ $total }});max-width:calc(100%/{{ $total }});">

              <div class="rounded-circle d-flex align-items-center justify-content-center {{ $circleClass }}"
                   style="width:44px;height:44px;margin-top:0px;z-index:1;">
                @if($failedNow)
                  <span class="fw-bold"><i class="bx bx-x fs-5"></i></span>
                @elseif($failedTrail)
                  <span class="fw-bold"><i class="bx bx-x fs-5"></i></span>
                @elseif($isActive)
                  <i class="bx bx-time-five fs-5"></i>
                @elseif($isCompleted)
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
