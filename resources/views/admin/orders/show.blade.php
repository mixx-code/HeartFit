@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Order #{{ $order->order_number }}</h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            <!-- Order Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Informasi Order</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>No. Order:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @switch(strtoupper($order->status))
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
                                        <span class="badge bg-light text-dark">{{ strtoupper($order->status ?? '—') }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pembayaran:</strong></td>
                            <td class="text-uppercase">{{ str_replace('_', ' ', $order->payment_method ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Dibuat:</strong></td>
                            <td>{{ $order->created_at ? $order->created_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Dibayar:</strong></td>
                            <td>{{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informasi Customer</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $order->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $order->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. HP:</strong></td>
                            <td>
                                @if($order->user?->detail?->hp)
                                    {{ $order->user->detail->hp }}
                                    @if(in_array(auth()->user()->role, ['ahli_gizi', 'superadmin']))
                                        <a href="https://wa.me/62{{ substr($order->user->detail->hp, 1) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-success ms-2"
                                           title="Chat via WhatsApp">
                                            <i class="bx bxl-whatsapp"></i> WA
                                        </a>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td>{{ $order->user?->detail?->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Package Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Informasi Paket</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td><strong>Nama Paket:</strong></td>
                                <td>{{ $order->package_label }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori:</strong></td>
                                <td>{{ $order->package_category ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Batch:</strong></td>
                                <td>{{ $order->package_batch ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Periode:</strong></td>
                                <td>
                                    {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M Y') : '-' }} 
                                    s.d 
                                    {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M Y') : '-' }}
                                    ({{ $order->days ?? 0 }} hari)
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Harga Paket:</strong></td>
                                <td>Rp {{ number_format($order->package_price ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Bayar:</strong></td>
                                <td><strong>Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Service Dates -->
            @if($order->service_dates && count($order->service_dates) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3">Tanggal Layanan</h6>
                    
                    @php
                        $serviceDates = $order->service_dates;
                        $totalDates = count($serviceDates);
                        $showAll = request('show_all_dates', false);
                    @endphp
                    
                    <div class="row g-2" id="serviceDatesContainer">
                        @if($totalDates <= 6)
                            {{-- If 6 or less dates, show all --}}
                            @foreach($serviceDates as $date)
                                <div class="col-auto">
                                    <div class="card card-body p-2 text-center border-0 bg-light">
                                        <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($date)->format('D') }}</div>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                        <div class="small">{{ \Carbon\Carbon::parse($date)->format('M Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Show first 5 dates --}}
                            @for($i = 0; $i < 5; $i++)
                                <div class="col-auto {{ !$showAll && $i >= 5 ? 'hidden-dates' : '' }}">
                                    <div class="card card-body p-2 text-center border-0 bg-light">
                                        <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('D') }}</div>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('d') }}</div>
                                        <div class="small">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('M Y') }}</div>
                                    </div>
                                </div>
                            @endfor
                            
                            {{-- Hidden dates indicator --}}
                            @if(!$showAll)
                                <div class="col-auto">
                                    <div class="card card-body p-2 text-center border-0 bg-secondary">
                                        <div class="small text-white">...</div>
                                        <div class="fw-bold text-white">{{ $totalDates - 6 }}</div>
                                        <div class="small text-white">lainnya</div>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Show last date --}}
                            <div class="col-auto">
                                <div class="card card-body p-2 text-center border-0 bg-light">
                                    <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($serviceDates[$totalDates - 1])->format('D') }}</div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($serviceDates[$totalDates - 1])->format('d') }}</div>
                                    <div class="small">{{ \Carbon\Carbon::parse($serviceDates[$totalDates - 1])->format('M Y') }}</div>
                                </div>
                            </div>
                            
                            {{-- Hidden dates (shown when toggle is active) --}}
                            @if($showAll)
                                @for($i = 5; $i < $totalDates - 1; $i++)
                                    <div class="col-auto">
                                        <div class="card card-body p-2 text-center border-0 bg-light">
                                            <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('D') }}</div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('d') }}</div>
                                            <div class="small">{{ \Carbon\Carbon::parse($serviceDates[$i])->format('M Y') }}</div>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        @endif
                    </div>
                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">Total: {{ $totalDates }} hari layanan</small>
                        
                        @if($totalDates > 6)
                            <button class="btn btn-sm btn-outline-primary" id="toggleDatesBtn" onclick="toggleAllDates()">
                                <i class="bx bx-{{ $showAll ? 'chevron-up' : 'chevron-down' }}"></i>
                                {{ $showAll ? 'Sembunyikan' : 'Lihat Semua' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($totalDates > 6)
            <script>
            function toggleAllDates() {
                const currentUrl = new URL(window.location);
                const isShowingAll = currentUrl.searchParams.get('show_all_dates') === '1';
                
                if (isShowingAll) {
                    currentUrl.searchParams.delete('show_all_dates');
                } else {
                    currentUrl.searchParams.set('show_all_dates', '1');
                }
                
                window.location.href = currentUrl.toString();
            }
            </script>
            @endif
            @endif

            <!-- Unique Menus -->
            @if($order->unique_menus && count($order->unique_menus) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Menu yang didapatkan ({{ $order->unique_menu_count ?? count($order->unique_menus) }} menu)</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($order->unique_menus as $menu)
                            <span class="badge bg-info text-white">{{ $menu }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Catatan Khusus (Hanya untuk paket personal) -->
            @if(!empty($order->notes) && strcasecmp($order->package_category ?? '', 'personal') === 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3">
                        <i class="bx bx-comment-dots me-2"></i>Catatan Khusus Customer
                        <small class="text-muted">(Paket Personal)</small>
                    </h6>
                    <div class="alert alert-info">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-info-circle me-2 mt-1"></i>
                            <div>
                                <p class="mb-0">{{ $order->notes }}</p>
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-time-five me-1"></i>
                                    Ditambahkan pada: {{ $order->updated_at ? $order->updated_at->format('d M Y H:i') : $order->created_at->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
