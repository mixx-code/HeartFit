@extends('layouts.app')
@section('title','Ringkasan Pesanan')

@section('content')
<div class="container py-5">
  <h3 class="fw-bold text-primary mb-3">Ringkasan Pesanan</h3>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Paket</dt>
            <dd class="col-sm-8">{{ $summary['package_label'] }}</dd>

            <dt class="col-sm-4">Kategori</dt>
            <dd class="col-sm-8">{{ $summary['package_category'] }}</dd>

            <dt class="col-sm-4">Batch</dt>
            <dd class="col-sm-8">{{ $summary['package_batch'] ?? '-' }}</dd>

            <dt class="col-sm-4">Periode</dt>
            <dd class="col-sm-8">
              {{ $summary['start_date'] }} s/d {{ $summary['end_date'] }} ({{ $summary['days'] }} hari)
              @if(!empty($summary['service_dates']))
                <button class="btn btn-link btn-sm p-0 ms-1 align-baseline" type="button"
                        data-bs-toggle="collapse" data-bs-target="#serviceDatesCollapse">
                  lihat tanggal
                </button>
              @endif
            </dd>

            @if(!empty($summary['service_dates']))
              <dt class="col-sm-4"></dt>
              <dd class="col-sm-8">
                <div id="serviceDatesCollapse" class="collapse">
                  <div class="border rounded p-2 bg-light small">
                    @foreach($summary['service_dates'] as $d)
                      <span class="badge rounded-pill text-bg-light border text-dark me-1 mb-1">{{ $d }}</span>
                    @endforeach
                  </div>
                </div>
              </dd>
            @endif

            <dt class="col-sm-4">List Menu</dt>
            <dd class="col-sm-8">
              {{ $summary['unique_menu_count'] ?? 0 }} menu
              @if(!empty($summary['unique_menus']))
                <div class="mt-2 d-flex flex-wrap gap-2">
                  @foreach(array_slice($summary['unique_menus'],0,8) as $m)
                    <span class="badge text-bg-light border text-black">{{ $m }}</span>
                  @endforeach
                  @if(($summary['unique_menu_count'] ?? count($summary['unique_menus'])) > 8)
                    <span class="badge text-bg-secondary">
                      +{{ ($summary['unique_menu_count'] ?? count($summary['unique_menus'])) - 8 }} lainnya
                    </span>
                  @endif
                </div>
              @endif
            </dd>

            <dt class="col-sm-4">Harga Paket</dt>
            <dd class="col-sm-8">Rp {{ number_format($summary['package_price'],0,',','.') }}</dd>

            <dt class="col-sm-4">Total Tagihan</dt>
            <dd class="col-sm-8 fw-semibold">Rp {{ number_format($summary['amount_total'],0,',','.') }}</dd>

            <dt class="col-sm-4">Metode Bayar</dt>
            <dd class="col-sm-8">{{ strtoupper(str_replace('_',' ',$summary['payment_method'])) }}</dd>
          </dl>
        </div>
      </div>

      {{-- Tombol Aksi --}}
      <div class="d-flex gap-2 mt-3">
        <a href="{{ route('orders.create') }}" class="btn btn-outline-secondary">Ubah Pesanan</a>

        {{-- Tombol PESAN: Simpan ke DB --}}
        <form action="{{ route('orders.store') }}" method="POST" class="ms-auto">
          @csrf

  {{-- Core --}}
  <input type="hidden" name="package_key"       value="{{ $summary['package_key'] }}">
  <input type="hidden" name="package_label"     value="{{ $summary['package_label'] }}">
  <input type="hidden" name="package_category"  value="{{ $summary['package_category'] }}">
  <input type="hidden" name="package_batch"     value="{{ $summary['package_batch'] ?? '' }}">
  <input type="hidden" name="package_price"     value="{{ $summary['package_price'] }}">
  <input type="hidden" name="amount_total"      value="{{ $summary['amount_total'] }}">
  <input type="hidden" name="start_date"        value="{{ $summary['start_date'] }}">
  <input type="hidden" name="end_date"          value="{{ $summary['end_date'] }}">
  <input type="hidden" name="days"              value="{{ $summary['days'] }}">
  <input type="hidden" name="payment_method"    value="{{ $summary['payment_method'] }}">
  {{-- Step 3 arrays as JSON --}}
  <input type="hidden" name="service_dates"     value='@json($summary["service_dates"])'>
  <input type="hidden" name="unique_menus"      value='@json($summary["unique_menus"])'>
  <input type="hidden" name="unique_menu_count" value="{{ $summary['unique_menu_count'] }}">

          <button type="submit" class="btn btn-success">
            Pesan
          </button>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold">Payload JSON (untuk integrasi API)</div>
        <div class="card-body">
          <pre class="mb-0" style="white-space: pre-wrap;">{{ $json }}</pre>
        </div>
      </div>

      @if(!empty($summary['unique_menus']))
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-light fw-semibold">Detail Menu Unik</div>
        <div class="card-body small">
          <ul class="mb-0">
            @foreach($summary['unique_menus'] as $m)
              <li>{{ $m }}</li>
            @endforeach
          </ul>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('button[form="placeOrderForm"]');
  if (btn) {
    btn.addEventListener('click', (e) => {
      const f = document.getElementById('placeOrderForm');
      if (!f) return;
      f.setAttribute('action', '{{ route('orders.store') }}');
      f.setAttribute('method', 'POST');
    });
  }
});
</script>

@endpush

@endsection
