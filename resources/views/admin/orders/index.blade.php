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

                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#reportModal">
                        <i class="bx bx-printer"></i> Laporan
                    </button>

                    {{-- Tombol buat order baru hanya untuk customer --}}
                    @if(auth()->user()->role === 'customer')
                        <a class="btn btn-success" href="{{ route('orders.create') }}">
                            <i class="bi bi-plus-circle"></i> Buat Order
                        </a>
                    @endif
                </div>
            </div>

            <div class="table-responsive text-nowrap" style="min-height: 400px">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No. Order</th>
                            <th>User</th>
                            <th>WhatsApp</th>
                            <th>Notes</th>
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

                                {{-- WhatsApp (dari tabel orders) --}}
                                <td>
                                    @if($o->whatsapp)
                                        <div class="d-flex align-items-center gap-1">
                                            <small class="text-muted">{{ $o->whatsapp }}</small>
                                            <a href="https://wa.me/{{ $o->whatsapp }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-success"
                                               title="Chat via WhatsApp">
                                                <i class="bx bxl-whatsapp"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Notes --}}
                                <td>
                                    <small>{{ Str::limit($o->notes, 50) ?? '—' }}</small>
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
                                        @case('SETTLEMENT')
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
                                        <a href="{{ route('admin.orders.show', $o) }}"
                                            class="btn btn-sm btn-outline-secondary">Detail</a>

                                        @if(auth()->user()->role === 'ahli_gizi')
                                            <a href="{{ route('admin.orders.struk', $o) }}" target="_blank"
                                                class="btn btn-sm btn-outline-dark">
                                                <i class="bx bx-printer"></i> Struk
                                            </a>
                                        @endif

                                        @if(auth()->user()->role === 'admin')
                                            <button type="button" class="btn btn-sm btn-outline-info btn-cetak-preview"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cetakModal"
                                                data-order-number="{{ $o->order_number }}"
                                                data-user-name="{{ $o->user?->name ?? '-' }}"
                                                data-user-email="{{ $o->user?->email ?? '-' }}"
                                                data-whatsapp="{{ $o->whatsapp ?? '-' }}"
                                                data-package-label="{{ $o->package_label }}"
                                                data-package-category="{{ $o->package_category ?? '-' }}"
                                                data-start-date="{{ $o->start_date ? $o->start_date->format('d/m/Y') : '-' }}"
                                                data-end-date="{{ $o->end_date ? $o->end_date->format('d/m/Y') : '-' }}"
                                                data-days="{{ $o->days ?? 0 }}"
                                                data-total="{{ number_format((int)($o->amount_total ?? $o->package_price), 0, ',', '.') }}"
                                                data-method="{{ strtoupper(str_replace('_', ' ', (string) $o->payment_method)) }}"
                                                data-status="{{ strtoupper($o->status ?? '-') }}"
                                                data-paid-at="{{ $o->paid_at ? $o->paid_at->format('d/m/Y H:i') : '-' }}"
                                                data-created="{{ $o->created_at ? $o->created_at->format('d/m/Y H:i') : '-' }}"
                                                data-notes="{{ $o->notes ?? '-' }}"
                                                data-pdf-url="{{ route('admin.orders.pdf', $o) }}"
                                            >
                                                <i class="bx bx-printer"></i> Print
                                            </button>
                                        @endif

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
                                    <td colspan="12" class="text-center text-muted">
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
    {{-- Modal Pilih Periode Laporan --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel"><i class="bx bx-printer me-2"></i>Generate Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" action="{{ route('admin.orders.report') }}" method="GET" target="_blank">
                        <div class="mb-3">
                            <label for="reportDateFrom" class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" class="form-control" id="reportDateFrom" name="date_from">
                        </div>
                        <div class="mb-3">
                            <label for="reportDateTo" class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="reportDateTo" name="date_to">
                        </div>
                        <div class="text-muted small mb-3">
                            <i class="bx bx-info-circle me-1"></i>Kosongkan tanggal untuk menampilkan semua data.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="reportForm" class="btn btn-warning">
                        <i class="bx bx-printer me-1"></i> Generate Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview Cetak (Admin) --}}
    <div class="modal fade" id="cetakModal" tabindex="-1" aria-labelledby="cetakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="cetakModalLabel"><i class="bx bx-file me-2"></i>Preview Detail Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="border rounded p-4" id="cetakPreviewContent">
                        <div class="text-center mb-3">
                            <h4 class="fw-bold mb-0">HEARTFIT NUTRITION</h4>
                            <small class="text-muted">Detail Order Customer</small>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-2">Informasi Order</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:130px">No. Order</td><td class="fw-semibold" id="cm-order-number"></td></tr>
                                    <tr><td class="text-muted">Status</td><td><span class="badge" id="cm-status"></span></td></tr>
                                    <tr><td class="text-muted">Metode</td><td id="cm-method"></td></tr>
                                    <tr><td class="text-muted">Tanggal Order</td><td id="cm-created"></td></tr>
                                    <tr><td class="text-muted">Tanggal Bayar</td><td id="cm-paid-at"></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-2">Informasi Customer</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:130px">Nama</td><td class="fw-semibold" id="cm-user-name"></td></tr>
                                    <tr><td class="text-muted">Email</td><td id="cm-user-email"></td></tr>
                                    <tr><td class="text-muted">WhatsApp</td><td id="cm-whatsapp"></td></tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-2">Informasi Paket</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:130px">Paket</td><td class="fw-semibold" id="cm-package-label"></td></tr>
                                    <tr><td class="text-muted">Kategori</td><td id="cm-package-category"></td></tr>
                                    <tr><td class="text-muted">Periode</td><td id="cm-periode"></td></tr>
                                    <tr><td class="text-muted">Durasi</td><td id="cm-days"></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-2">Pembayaran</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:130px">Total</td><td class="fw-bold text-success fs-5" id="cm-total"></td></tr>
                                </table>
                            </div>
                        </div>
                        <div id="cm-notes-section">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Catatan</h6>
                            <div class="alert alert-warning py-2 mb-0">
                                <small id="cm-notes"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="cm-download-btn" class="btn btn-primary" target="_blank">
                        <i class="bx bx-download me-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-cetak-preview').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const d = this.dataset;
                document.getElementById('cm-order-number').textContent = d.orderNumber;
                document.getElementById('cm-user-name').textContent = d.userName;
                document.getElementById('cm-user-email').textContent = d.userEmail;
                document.getElementById('cm-whatsapp').textContent = d.whatsapp;
                document.getElementById('cm-package-label').textContent = d.packageLabel;
                document.getElementById('cm-package-category').textContent = d.packageCategory;
                document.getElementById('cm-periode').textContent = d.startDate + ' s/d ' + d.endDate;
                document.getElementById('cm-days').textContent = d.days + ' hari';
                document.getElementById('cm-total').textContent = 'Rp ' + d.total;
                document.getElementById('cm-method').textContent = d.method;
                document.getElementById('cm-created').textContent = d.created;
                document.getElementById('cm-paid-at').textContent = d.paidAt;
                document.getElementById('cm-download-btn').href = d.pdfUrl;

                const status = d.status;
                const badge = document.getElementById('cm-status');
                badge.textContent = status;
                badge.className = 'badge';
                if (status === 'PAID' || status === 'SETTLEMENT') badge.classList.add('bg-success');
                else if (status === 'UNPAID') badge.classList.add('bg-warning', 'text-dark');
                else if (status === 'EXPIRED') badge.classList.add('bg-secondary');
                else if (status === 'CANCELED') badge.classList.add('bg-danger');
                else badge.classList.add('bg-light', 'text-dark');

                const notesSection = document.getElementById('cm-notes-section');
                if (d.notes && d.notes !== '-') {
                    notesSection.style.display = '';
                    document.getElementById('cm-notes').textContent = d.notes;
                } else {
                    notesSection.style.display = 'none';
                }
            });
        });
    });
    </script>
    @endpush

    @endsection
