@extends('layouts.app')
@section('title','Selesai')

@section('content')
<div class="container py-5">
  <div class="text-center">
    <h3 class="fw-bold text-primary mb-2">Terima kasih!</h3>
    <p class="text-muted">Nomor Order: <strong>{{ $order->order_number }}</strong></p>

    @if($order->status === 'PAID')
      <div class="alert alert-success mx-auto" style="max-width:640px;">
        Pembayaran <strong>berhasil</strong>. Kami akan segera memproses pesanan Anda.
      </div>
    @elseif($order->payment_method === 'cod')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        Metode <strong>COD</strong> dipilih. Silakan siapkan pembayaran saat pesanan diantar.
      </div>
    @else
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        Status saat ini: <strong>{{ $order->status }}</strong>. Jika Anda sudah membayar, halaman ini akan diperbarui setelah notifikasi diterima.
      </div>
    @endif

    <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">Pesan Lagi</a>
  </div>

  <div class="card border-0 shadow-sm mt-4 mx-auto" style="max-width:720px;">
    <div class="card-body">
      <h5 class="fw-semibold mb-3">Ringkasan</h5>
      <dl class="row mb-0">
        <dt class="col-sm-4">Paket</dt>
        <dd class="col-sm-8">{{ $order->package_label }} ({{ $order->package_category }})</dd>

        <dt class="col-sm-4">Periode</dt>
        <dd class="col-sm-8">{{ $order->start_date->toDateString() }} s/d {{ $order->end_date->toDateString() }} ({{ $order->days }} hari)</dd>

        <dt class="col-sm-4">Total</dt>
        <dd class="col-sm-8">Rp {{ number_format($order->amount_total ?? $order->package_price,0,',','.') }}</dd>

        <dt class="col-sm-4">Metode</dt>
        <dd class="col-sm-8">{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</dd>

        <dt class="col-sm-4">Status</dt>
        <dd class="col-sm-8"><span class="badge {{ $order->status === 'PAID' ? 'bg-success' : ($order->status === 'UNPAID' ? 'bg-warning' : 'bg-secondary') }}">{{ $order->status }}</span></dd>
      </dl>
    </div>
  </div>
</div>
@endsection
