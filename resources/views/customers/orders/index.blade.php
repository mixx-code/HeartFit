@extends('layouts.app')

@section('title', 'Orders')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <h5 class="mb-0">Daftar Orders</h5>

      <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
        {{-- Search + Page size (GET) --}}
        <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('orders.index') /* ganti ke route index orders milikmu jika berbeda */ }}">
          <div class="input-group" style="min-width: 280px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Cari nomor order / paket">
          </div>
          <div class="input-group" style="max-width: 160px;">
            <span class="input-group-text">Rows</span>
            <select name="per_page" class="form-select" onchange="this.form.submit()">
              @foreach ([5,10,15,20] as $size)
                <option value="{{ $size }}" {{ (int) request('per_page', $perPage ?? 10) === $size ? 'selected' : '' }}>
                  {{ $size }}
                </option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-primary" type="submit">Search</button>
          @if (request('q'))
            <a href="{{ url()->current() }}?per_page={{ request('per_page', $perPage ?? 10) }}" class="btn btn-outline-secondary">Reset</a>
          @endif
        </form>

        {{-- (Opsional) tombol buat order baru --}}
        <a class="btn btn-success" href="{{ route('orders.create') }}">
          <i class="bi bi-plus-circle"></i> Buat Order
        </a>
      </div>
    </div>

    <div class="table-responsive text-nowrap" style="min-height: 400px">
      <table class="table">
        <thead class="table-light">
          <tr>
            <th>No. Order</th>
            <th>Paket</th>
            <th>Periode</th>
            <th>Total</th>
            <th>Metode</th>
            <th>Status</th>
            <th style="width: 1%;">Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse($orders as $o)
            @php
              $total = $o->amount_total ?? $o->package_price;
              $isUnpaid = $o->status === 'UNPAID';
            @endphp
            <tr>
              <td class="fw-semibold">{{ $o->order_number }}</td>
              <td>
                {{ $o->package_label }}
                <div class="small text-muted">{{ $o->package_category }}</div>
              </td>
              <td>
                {{ optional($o->start_date)->toDateString() }} — {{ optional($o->end_date)->toDateString() }}
                <div class="small text-muted">{{ $o->days }} hari</div>
              </td>
              <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
              <td class="text-uppercase">{{ str_replace('_', ' ', $o->payment_method) }}</td>
              <td>
                @switch($o->status)
                  @case('PAID')
                    <span class="badge bg-success">PAID</span>
                    @break
                  @case('UNPAID')
                    <span class="badge bg-warning text-dark">UNPAID</span>
                    @break
                  @case('EXPIRED')
                    <span class="badge bg-secondary">EXPIRED</span>
                    @break
                  @case('CANCELED')
                    <span class="badge bg-danger">CANCELED</span>
                    @break
                  @default
                    <span class="badge bg-light text-dark">{{ $o->status }}</span>
                @endswitch
              </td>
              <td>
                <div class="d-flex gap-1">
                  {{-- Lihat ringkasan/hasil --}}
                  <a href="{{ route('orders.finish', $o) }}" class="btn btn-sm btn-outline-secondary">Detail</a>

                  {{-- Action khusus UNPAID --}}
                  @if($isUnpaid)
                    @if($o->payment_method === 'cod')
                      <a href="{{ route('orders.finish', $o) }}" class="btn btn-sm btn-info">Instruksi COD</a>
                    @else
                      <a href="{{ route('orders.pay', $o) }}" class="btn btn-sm btn-primary">Bayar</a>
                    @endif
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">
                Tidak ada data{{ request('q') ? ' untuk pencarian ini' : '' }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <div class="small text-muted">
        @if ($orders->total() > 0)
          Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} data
        @else
          Menampilkan 0–0 dari 0 data
        @endif
      </div>
      {{-- Pagination + pertahankan query (q, per_page) --}}
      {{ $orders->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
    </div>
  </div>
</div>
@endsection
