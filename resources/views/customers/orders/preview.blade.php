@extends('layouts.app')
@section('title','Ringkasan Pesanan')

@section('content')
<div class="container py-5">
  <h3 class="fw-bold text-primary mb-3">Ringkasan Pesanan (Statis)</h3>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Paket</dt>
            <dd class="col-sm-8">{{ $summary['package_label'] }}</dd>

            <dt class="col-sm-4">Kategori</dt>
            <dd class="col-sm-8">{{ $summary['package_category'] }}</dd>

            <dt class="col-sm-4">Periode</dt>
            <dd class="col-sm-8">{{ $summary['start_date'] }} s/d {{ $summary['end_date'] }} ({{ $summary['days'] }} hari)</dd>

            <dt class="col-sm-4">Harga</dt>
            <dd class="col-sm-8">Rp {{ number_format($summary['package_price'],0,',','.') }}</dd>

            <dt class="col-sm-4">Metode Bayar</dt>
            <dd class="col-sm-8">{{ strtoupper(str_replace('_',' ',$summary['payment_method'])) }}</dd>
          </dl>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a href="{{ route('orders.create') }}" class="btn btn-outline-secondary">Ubah Pesanan</a>
        <button class="btn btn-success" onclick="alert('Simulasi: Pesanan dikonfirmasi (tanpa database).')">Konfirmasi (Simulasi)</button>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold">Payload JSON (untuk integrasi API nanti)</div>
        <div class="card-body">
          <pre class="mb-0" style="white-space: pre-wrap;">{{ $json }}</pre>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
