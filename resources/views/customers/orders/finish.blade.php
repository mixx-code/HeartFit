@extends('layouts.app')
@section('title','Selesai')

@push('styles')
<style>
  .page-wrap { max-width: 760px; }
  .kv-card .kv-row + .kv-row{border-top:1px dashed #eef2f7}
  .kv-label{
    flex:0 0 160px; max-width:160px;
    font-size:.8rem; text-transform:uppercase; letter-spacing:.04em;
    color:var(--bs-secondary-color);
  }
  .kv-value{flex:1; color:var(--bs-emphasis-color); font-weight:600}
  .kv-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace}
  .badge-soft{
    background:#f6f9ff; border:1px solid #e5edff; color:#3b82f6; font-weight:600;
  }
  .section-title{letter-spacing:.02em}
</style>
@endpush

@section('content')
@php
  use Carbon\Carbon;
  $start = $order->start_date instanceof \Carbon\Carbon ? $order->start_date : Carbon::parse($order->start_date);
  $end   = $order->end_date   instanceof \Carbon\Carbon ? $order->end_date   : Carbon::parse($order->end_date);

  $grandTotal   = (int)($order->amount_total ?? $order->package_price ?? 0);
  $methodLabel  = strtoupper(str_replace('_',' ', $order->payment_method ?? '-'));
  $statusBadge  = match($order->status){
    'PAID'    => 'bg-success',
    'PENDING' => 'bg-info',
    'FAILED'  => 'bg-danger',
    'EXPIRED' => 'bg-secondary',
    'UNPAID'  => 'bg-warning',
    default   => 'bg-secondary',
  };
@endphp

<div class="container py-5 d-flex justify-content-center">
  <div class="page-wrap w-100">

    {{-- Header --}}
    <div class="text-center mb-3">
      <h3 class="fw-bold text-primary mb-2">Terima kasih!</h3>
      <p class="text-muted mb-1">Nomor Order:</p>
      <div class="kv-mono fs-6">{{ $order->order_number }}</div>
    </div>

    {{-- Alert Status --}}
    @if($order->status === 'PAID')
      <div class="alert alert-success mx-auto" style="max-width:640px;">
        Pembayaran <strong>berhasil</strong>. Kami akan segera memproses pesanan Anda.
        @if(!empty($order->paid_at))
          <div class="mt-1 small mb-0 text-success-700">Dibayar pada: <strong>{{ \Carbon\Carbon::parse($order->paid_at)->toDateTimeString() }}</strong></div>
        @endif
      </div>
    @elseif($order->status === 'UNPAID' && $order->payment_method === 'transfer')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <strong>Menunggu pembayaran</strong>. Halaman ini akan otomatis diperbarui setelah pembayaran berhasil.
            <div class="small text-muted mt-1">
              <i class="bx bx-time me-1"></i>Auto-check setiap 5 detik (maksimal 5 menit)
            </div>
          </div>
          <button onclick="manualCheck()" class="btn btn-sm btn-outline-info">
            <i class="bx bx-refresh me-1"></i>Cek Sekarang
          </button>
        </div>
      </div>
    @elseif($order->status === 'EXPIRED')
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        <div class="d-flex align-items-center">
          <i class="bx bx-error-circle me-2 fs-5"></i>
          <div>
            <strong>Pembayaran expired</strong>. Anda memiliki dua pilihan:
            <ul class="mb-0 mt-2 small">
              <li><strong>Bayar Ulang</strong> - Lanjutkan pembayaran order yang sama (waktu: 5 menit)</li>
              <li><strong>Pesan Ulang</strong> - Buat order baru dengan paket yang sama</li>
            </ul>
          </div>
        </div>
      </div>
    @elseif(($order->payment_method ?? '') === 'cod')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        Metode <strong>COD</strong> dipilih. Silakan siapkan pembayaran saat pesanan diantar.
      </div>
    @else
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        Status saat ini: <strong>{{ $order->status }}</strong>. 
        @if($order->payment_method === 'transfer')
          Halaman ini akan otomatis diperbarui setelah pembayaran berhasil.
          <button onclick="manualCheck()" class="btn btn-sm btn-outline-warning ms-2">
            <i class="bx bx-refresh me-1"></i>Cek Status
          </button>
        @else
          Jika Anda sudah membayar, halaman ini akan diperbarui setelah notifikasi diterima.
        @endif
      </div>
    @endif

    <div class="text-center">
      @if($order->status === 'EXPIRED')
        {{-- Bayar Ulang untuk transfer --}}
        @if($order->payment_method === 'transfer')
          <a href="{{ route('orders.pay', $order) }}" class="btn btn-success mt-2">
            <i class="bx bx-time me-1"></i>Bayar Ulang (5 Menit)
          </a>
        @endif
        <a href="{{ route('orders.create') }}?package_key={{ $order->package_key }}" class="btn btn-warning mt-2">
          <i class="bx bx-refresh me-1"></i>Pesan Ulang Paket
        </a>
      @endif
      <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">Pesan Paket Lain</a>
      <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary mt-2">Lihat Orderan Kamu</a>
    </div>

    {{-- Ringkasan --}}
    <div class="card border-0 shadow-sm mt-4 mx-auto kv-card">
      <div class="card-body">
        <h5 class="fw-semibold mb-3 text-center section-title">Ringkasan</h5>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Paket</div>
          <div class="kv-value fw-semibold">
            {{ $order->package_label }}
            <span class="text-muted fw-normal">({{ $order->package_category }})</span>
            @if(!empty($order->package_batch))
              <span class="badge badge-soft rounded-pill ms-1">BATCH: {{ $order->package_batch }}</span>
            @endif
          </div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Periode</div>
          <div class="kv-value">
            {{ $start->toDateString() }} s/d {{ $end->toDateString() }}
            <span class="text-muted fw-normal">({{ (int)$order->days }} hari)</span>
          </div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Total</div>
          <div class="kv-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Metode</div>
          <div class="kv-value">{{ $methodLabel }}</div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Status</div>
          <div class="kv-value">
            <span class="badge {{ $statusBadge }}">{{ $order->status }}</span>
          </div>
        </div>

        @if(!empty($order->notes) && strcasecmp($order->package_category ?? '', 'personal') === 0)
          <div class="kv-row d-flex align-items-start py-2">
            <div class="kv-label">Catatan</div>
            <div class="kv-value">
              <div class="alert alert-info small mb-0">
                <i class="bx bx-info-circle me-1"></i>
                {{ $order->notes }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>

{{-- Auto-refresh status untuk pembayaran --}}
@if(in_array($order->status, ['UNPAID', 'EXPIRED']) && $order->payment_method === 'transfer')
@push('scripts')
<script>
// Auto-check payment status every 5 seconds
let checkInterval;
let isChecking = false;

async function checkPaymentStatus() {
    if (isChecking) return;
    isChecking = true;
    
    try {
        const response = await fetch(`{{ route('orders.check-payment', $order) }}`);
        const data = await response.json();
        
        console.log('Payment status check:', data);
        
        if (data.status_changed) {
            // Status berubah, reload page untuk update UI
            clearInterval(checkInterval);
            
            // Tampilkan notifikasi
            if (data.status === 'PAID') {
                alert('✅ ' + data.message);
            } else if (data.status === 'EXPIRED') {
                alert('⏰ ' + data.message);
            }
            
            // Reload page
            window.location.reload();
        }
    } catch (error) {
        console.error('Error checking payment status:', error);
    } finally {
        isChecking = false;
    }
}

// Mulai auto-check
document.addEventListener('DOMContentLoaded', function() {
    // Check setiap 5 detik
    checkInterval = setInterval(checkPaymentStatus, 5000);
    
    // Stop checking setelah 5 menit (300 detik)
    setTimeout(() => {
        clearInterval(checkInterval);
        console.log('Auto-check stopped after 5 minutes');
    }, 300000);
});

// Manual check button (opsional)
function manualCheck() {
    checkPaymentStatus();
}
</script>
@endpush
@endif
@endsection
