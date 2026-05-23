@extends('layouts.app')
@section('title','Ringkasan Pesanan')

@section('content')
<div class="container py-5">

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light">
          <h5 class="mb-0">Ringkasan Pesanan</h5>
        </div>

        <div class="card-body px-4 py-3">

          <div class="mb-3">
            <label class="form-label text-muted small">Periode Layanan</label>
            <div class="fw-semibold">{{ $summary['start_date'] }} &mdash; {{ $summary['end_date'] }}</div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted small">Paket</label>
            <div class="fw-semibold">{{ $summary['package_label'] }}</div>
            <div class="text-muted">{{ ucfirst($summary['package_category']) }} &bull; Batch {{ $summary['package_batch'] ?? '-' }}</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label text-muted small">Menu ({{ $summary['unique_menu_count'] ?? 0 }})</label>
            <div class="small">
              @if(!empty($summary['unique_menus']))
                {{ implode(', ', $summary['unique_menus']) }}
              @endif
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted small">Metode Pembayaran</label>
            <div class="fw-semibold">{{ strtoupper(str_replace('_',' ',$summary['payment_method'])) }}</div>
          </div>

          @if(!empty($summary['notes']))
          <div class="mb-3">
            <label class="form-label text-muted small">Catatan Khusus</label>
            <div class="form-text">{{ $summary['notes'] }}</div>
          </div>
          @endif

          @if(!empty($summary['whatsapp']))
          <div class="mb-3">
            <label class="form-label text-muted small">Nomor WhatsApp</label>
            <div class="fw-semibold">{{ $summary['whatsapp'] }}</div>
          </div>
          @endif

          <div class="card bg-light">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span>Harga Paket</span>
                <span>Rp {{ number_format($summary['package_price'],0,',','.') }}</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span class="text-primary">Rp {{ number_format($summary['amount_total'],0,',','.') }}</span>
              </div>
            </div>
          </div>

        </div>{{-- end card-body --}}

        <div class="card-footer">
          <div class="d-flex gap-2">
            <a href="{{ route('orders.create') }}" class="btn btn-secondary">Ubah</a>
            <form action="{{ route('orders.store') }}" method="POST" class="ms-auto">
              @csrf
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
              <input type="hidden" name="service_dates"     value='@json($summary["service_dates"])'>
              <input type="hidden" name="unique_menus"      value='@json($summary["unique_menus"])'>
              <input type="hidden" name="unique_menu_count" value="{{ $summary['unique_menu_count'] }}">
              <input type="hidden" name="notes"             value="{{ $summary['notes'] ?? '' }}">
              <input type="hidden" name="whatsapp"          value="{{ $summary['whatsapp'] ?? '' }}">
              <button type="submit" class="btn btn-primary">Konfirmasi Pesanan</button>
            </form>
          </div>
        </div>

      </div>{{-- end card --}}
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
