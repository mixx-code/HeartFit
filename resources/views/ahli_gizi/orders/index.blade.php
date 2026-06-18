@extends('layouts.app')

@section('title', 'Dashboard Ahli Gizi')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Total Customer</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['total_customers']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Total Orders</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['total_orders']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-receipt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Order Aktif Hari Ini</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['active_today']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1 text-muted">Orders Bulan Ini</span>
                            <h3 class="card-title mb-0">{{ number_format($summary['orders_this_month']) }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Order Customer - Paket Personal</h5>
            <small class="text-muted float-end">Daftar order customer yang perlu konsultasi</small>
        </div>

        {{-- Search & Pagination --}}
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 mb-3">
                <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('ahli_gizi.orders') }}">
                    <div class="input-group" style="min-width: 280px;">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Cari order...">
                    </div>
                    <div class="input-group" style="max-width: 160px;">
                        <span class="input-group-text">Rows</span>
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            @foreach ([5, 10, 15, 20] as $size)
                                <option value="{{ $size }}"
                                    {{ (int) request('per_page', $perPage) === $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if (request('q'))
                        <a href="{{ route('ahli_gizi.orders', ['per_page' => request('per_page', $perPage)]) }}"
                            class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive text-nowrap" style="min-height: 400px">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No. Order</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Paket</th>
                            <th>Nomor WA</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->user->email }}</td>
                                <td>{{ $order->package_label }}</td>
                                <td>
                                    @if($order->user->detail && $order->user->detail->hp)
                                        <span class="badge bg-label-success">{{ $order->user->detail->hp }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                                                <i class="bx bx-detail me-1"></i> Detail
                                            </a>
                                            @if($order->user->detail && $order->user->detail->hp)
                                                <a class="dropdown-item" href="{{ route('ahli_gizi.wa', $order->user->id) }}" target="_blank">
                                                    <i class="bx bx-message-square-dots me-1"></i> WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Tidak ada data order paket personal{{ request('q') ? ' untuk pencarian ini' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="small text-muted">
                    @if($orders->total() > 0)
                        Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} data
                    @else
                        Menampilkan 0–0 dari 0 data
                    @endif
                </div>
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
            </div>
        </div>
    </div>
</div>
@endsection
