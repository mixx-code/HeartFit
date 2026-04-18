<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Order - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 20px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #333;
        }
        
        .package-info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .package-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .package-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .package-detail-item {
            text-align: center;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        
        .package-detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #3498db;
        }
        
        .package-detail-label {
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 2px;
        }
        
        .dates-list {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .date-item {
            padding: 3px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .date-item:last-child {
            border-bottom: none;
        }
        
        .notes-section {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        
        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        
        .notes-content {
            color: #856404;
            font-style: italic;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-unpaid { background: #f8d7da; color: #721c24; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        .status-expired { background: #f8d7da; color: #721c24; }
        .status-canceled { background: #e2e3e5; color: #383d41; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: #f0f0f0;
            z-index: -1;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="watermark">HeartFit</div>
    
    <!-- Header -->
    <div class="header">
        <h1>HEARTFIT NUTRITION</h1>
        <p>Jasa Catering Makanan Sehat</p>
        <p>Detail Order Customer</p>
    </div>
    
    <!-- Order Information -->
    <div class="section">
        <div class="section-title">Informasi Order</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nomor Order</div>
                <div class="info-value">{{ $order->order_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Order</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">{{ strtoupper($order->payment_method ?? '-') }}</div>
            </div>
        </div>
    </div>
    
    <!-- Customer Information -->
    <div class="section">
        <div class="section-title">Informasi Customer</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nama</div>
                <div class="info-value">{{ $order->user->name ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $order->user->email ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">No. Telepon</div>
                <div class="info-value">{{ $order->user->phone ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Alamat</div>
                <div class="info-value">{{ $order->user->address ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Package Information -->
    <div class="section">
        <div class="section-title">Informasi Paket</div>
        <div class="package-info">
            <div class="package-name">{{ $order->package_label ?? '-' }}</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">{{ ucfirst($order->package_category ?? '-') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Batch</div>
                    <div class="info-value">{{ $order->package_batch ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Harga</div>
                    <div class="info-value">Rp. {{ number_format($order->package_price ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            
            <div class="package-details">
                <div class="package-detail-item">
                    <div class="package-detail-value">{{ $order->days ?? 0 }}</div>
                    <div class="package-detail-label">Hari</div>
                </div>
                <div class="package-detail-item">
                    <div class="package-detail-value">{{ $order->unique_menu_count ?? 0 }}</div>
                    <div class="package-detail-label">Menu Unik</div>
                </div>
                <div class="package-detail-item">
                    <div class="package-detail-value">{{ $order->total_meals ?? 0 }}</div>
                    <div class="package-detail-label">Total Makanan</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Dates -->
    <div class="section">
        <div class="section-title">Tanggal Layanan</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Periode</div>
                <div class="info-value">
                    @if($order->start_date && $order->end_date)
                        {{ \Carbon\Carbon::parse($order->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($order->end_date)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Hari</div>
                <div class="info-value">{{ $order->days ?? 0 }} hari</div>
            </div>
        </div>
        
        @if($order->service_dates && count($order->service_dates) > 0)
            <div style="margin-top: 15px;">
                <div class="info-label">Daftar Tanggal:</div>
                <div class="dates-list">
                    @foreach(array_slice($order->service_dates, 0, 20) as $date)
                        <div class="date-item">{{ \Carbon\Carbon::parse($date)->format('d/m/Y (l)') }}</div>
                    @endforeach
                    @if(count($order->service_dates) > 20)
                        <div class="date-item">... dan {{ count($order->service_dates) - 20 }} tanggal lainnya</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <!-- Notes (if any) -->
    @if(!empty($order->notes) && strcasecmp($order->package_category ?? '', 'personal') === 0)
    <div class="section">
        <div class="section-title">Catatan Customer</div>
        <div class="notes-section">
            <div class="notes-title">Catatan Khusus:</div>
            <div class="notes-content">{{ $order->notes }}</div>
        </div>
    </div>
    @endif
    
    <!-- Payment Information -->
    <div class="section">
        <div class="section-title">Informasi Pembayaran</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Total Harga</div>
                <div class="info-value">Rp. {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status Pembayaran</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            @if($order->paid_at)
            <div class="info-item">
                <div class="info-label">Tanggal Bayar</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem HeartFit Nutrition</p>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
