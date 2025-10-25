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
    @elseif(($order->payment_method ?? '') === 'cod')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        Metode <strong>COD</strong> dipilih. Silakan siapkan pembayaran saat pesanan diantar.
      </div>
    @else
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        Status saat ini: <strong>{{ $order->status }}</strong>. Jika Anda sudah membayar, halaman ini akan diperbarui setelah notifikasi diterima.
      </div>
    @endif

    <div class="text-center">
      <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">Pesan Lagi</a>
      <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary mt-2">Liat Orderan Kamu</a>
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
      </div>
    </div>

  </div>
</div>
@endsection
