@extends('layouts.app')

@section('title', 'Orders')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <h5 class="mb-0">Daftar Orders</h5>

                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                    {{-- Search + Page size (GET) --}}
                    <form class="d-flex align-items-center gap-2" method="GET"
                        action="{{ route('admin.orders.index');}}">
                        <div class="input-group" style="min-width: 280px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nomor order / paket">
                        </div>
                        <div class="input-group" style="max-width: 160px;">
                            <span class="input-group-text">Rows</span>
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach ([5, 10, 15, 20] as $size)
                                    <option value="{{ $size }}"
                                        {{ (int) request('per_page', $perPage ?? 10) === $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Search</button>
                        @if (request('q'))
                            <a href="{{ url()->current() }}?per_page={{ request('per_page', $perPage ?? 10) }}"
                                class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>

                    {{-- (Opsional) tombol buat order baru --}}
                    <a class="btn btn-success" href="">
                        <i class="bi bi-plus-circle"></i> Buat Order
                    </a>
                </div>
            </div>

            <div class="table-responsive text-nowrap" style="min-height: 400px">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No. Order</th>
                            <th>User</th>
                            <th>Paket</th>
                            <th>Periode</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th>Created</th>
                            <th style="width:1%;">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $o)
                            @php
                                $total = $o->amount_total ?? $o->package_price;
                                $isUnpaid = strtoupper($o->status) === 'UNPAID';

                                // helper format tanggal (aman untuk string/Carbon)
                                $fmt = function ($dt, $withTime = false) {
                                    if (!$dt) {
                                        return null;
                                    }
                                    try {
                                        $c = \Illuminate\Support\Carbon::parse($dt);
                                        return $withTime ? $c->format('Y-m-d H:i') : $c->format('Y-m-d');
                                    } catch (\Throwable $e) {
                                        return (string) $dt;
                                    }
                                };
                            @endphp

                            <tr>
                                {{-- No. Order --}}
                                <td class="fw-semibold">{{ $o->order_number }}</td>

                                {{-- User (name + email, info soft-deleted) --}}
                                <td>
                                    {{ $o->user?->name ?? '—' }}
                                    <div class="small text-muted">
                                        {{ $o->user?->email ?? '' }}
                                        @if ($o->user && method_exists($o->user, 'trashed') && $o->user->trashed())
                                            <br><span class="badge bg-secondary">User dihapus</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Paket (label + kategori/batch) --}}
                                <td>
                                    {{ $o->package_label }}
                                    <div class="small text-muted">
                                        {{ $o->package_category ?? '—' }}
                                        @if (!empty($o->package_batch))
                                            • Batch {{ $o->package_batch }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Periode --}}
                                <td>
                                    {{ $fmt($o->start_date) }} — {{ $fmt($o->end_date) }}
                                    <div class="small text-muted">{{ (int) ($o->days ?? 0) }} hari</div>
                                </td>

                                {{-- Total --}}
                                <td>Rp {{ number_format((int) $total, 0, ',', '.') }}</td>

                                {{-- Metode --}}
                                <td class="text-uppercase">{{ str_replace('_', ' ', (string) $o->payment_method) }}</td>

                                {{-- Status --}}
                                <td>
                                    @switch(strtoupper($o->status))
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
                                            <span class="badge bg-light text-dark">{{ strtoupper($o->status ?? '—') }}</span>
                                    @endswitch
                                </td>

                                {{-- Paid At --}}
                                <td>{{ $o->paid_at ? $fmt($o->paid_at, true) : '—' }}</td>

                                {{-- Created --}}
                                <td>{{ $o->created_at ? $fmt($o->created_at, true) : '—' }}</td>

                                {{-- Actions (sesuaikan route adminmu jika berbeda) --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href=""
                                            class="btn btn-sm btn-outline-secondary">Detail</a>

                                        @if ($isUnpaid)
                                            @if (($o->payment_method ?? '') === 'cod')
                                                <a href=""
                                                    class="btn btn-sm btn-info">Instruksi COD</a>
                                            @else
                                                {{-- @if (Route::has('admin.orders.pay'))
                                                    <a href="{{ route('admin.orders.pay', $o) }}"
                                                        class="btn btn-sm btn-primary">Tagih/Bayar</a>
                                                @endif --}}
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
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
