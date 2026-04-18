<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Order - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 2px;
            size: 80mm 100mm; /* Lebih pendek */
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            line-height: 1.0;
            color: #000;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .receipt {
            width: 100%;
            max-width: 220px;
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #000;
            background: white;
            box-sizing: border-box;
        }
        
        .header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }
        
        .header h1 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }
        
        .header p {
            margin: 1px 0;
            font-size: 7px;
            color: #666;
        }
        
        .section {
            margin-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #000;
        }
        
        .info-value {
            text-align: right;
            font-weight: normal;
        }
        
        .package-info {
            background: #f5f5f5;
            padding: 4px;
            border: 1px solid #ddd;
            margin: 5px 0;
            text-align: center;
        }
        
        .package-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }
        
        .menu-list {
            background: #f9f9f9;
            padding: 4px;
            border: 1px solid #ddd;
            margin: 5px 0;
        }
        
        .menu-item {
            padding: 1px 0;
            font-size: 8px;
            border-bottom: 1px dotted #ccc;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .dates-info {
            background: #f5f5f5;
            padding: 4px;
            border: 1px solid #ddd;
            margin: 5px 0;
        }
        
        .dates-list {
            font-size: 7px;
        }
        
        .date-item {
            padding: 1px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .date-item:last-child {
            border-bottom: none;
        }
        
        .notes-box {
            background: #fffacd;
            padding: 4px;
            border: 1px solid #ddd;
            margin: 5px 0;
            font-size: 7px;
            font-style: italic;
        }
        
        .total-box {
            background: #000;
            color: white;
            padding: 6px;
            margin: 8px 0;
            text-align: center;
        }
        
        .total-label {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .total-amount {
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 7px;
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
            text-align: center;
            margin-top: 8px;
            padding-top: 3px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #666;
        }
        
        .divider {
            height: 1px;
            background: #ddd;
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>HEARTFIT</h1>
            <p>STRUK PEMESANAN</p>
        </div>
        
        <!-- Order Info -->
        <div class="section">
            <div class="info-row">
                <span class="info-label">No:</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tgl:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ $order->status }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $order->user->name ?? '-' }}</span>
            </div>
        </div>
        
        <!-- Package Info -->
        <div class="section">
            <div class="package-info">
                <div class="package-name">{{ $order->package_label ?? '-' }}</div>
                <div class="info-row">
                    <span>{{ ucfirst($order->package_category ?? '-') }}</span>
                    <span>{{ $order->days ?? 0 }} hari</span>
                </div>
                <div class="info-row">
                    <span>Rp {{ number_format($order->package_price ?? 0, 0, ',', '.') }}</span>
                    @if($order->package_batch)
                    <span>Batch {{ $order->package_batch }}</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Menu -->
        @if($order->unique_menus && count($order->unique_menus) > 0)
        <div class="section">
            <div class="menu-list">
                @foreach(array_slice($order->unique_menus, 0, 3) as $menu)
                    <div class="menu-item">{{ $menu }}</div>
                @endforeach
                @if(count($order->unique_menus) > 3)
                    <div class="menu-item">... dan {{ count($order->unique_menus) - 3 }} menu lain</div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Dates -->
        @if($order->start_date && $order->end_date)
        <div class="section">
            <div class="dates-info">
                <div class="info-row">
                    <span>Mulai:</span>
                    <span>{{ \Carbon\Carbon::parse($order->start_date)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span>Selesai:</span>
                    <span>{{ \Carbon\Carbon::parse($order->end_date)->format('d/m/Y') }}</span>
                </div>
                <div class="dates-list">
                    @foreach(array_slice($order->service_dates, 0, 3) as $date)
                        <div class="date-item">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
                    @endforeach
                    @if(count($order->service_dates) > 3)
                        <div class="date-item">+{{ count($order->service_dates) - 3 }} hari</div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <!-- Notes -->
        @if(!empty($order->notes) && strcasecmp($order->package_category ?? '', 'personal') === 0)
        <div class="section">
            <div class="notes-box">{{ $order->notes }}</div>
        </div>
        @endif
        
        <!-- Divider -->
        <div class="divider"></div>
        
        <!-- Total -->
        <div class="section">
            <div class="total-box">
                <div class="total-label">Total</div>
                <div class="total-amount">Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="section">
            <div class="info-row">
                <span class="info-label">Metode:</span>
                <span class="info-value">{{ strtoupper($order->payment_method ?? '-') }}</span>
            </div>
            @if($order->paid_at)
            <div class="info-row">
                <span class="info-label">Bayar:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih</p>
            <p>heartfit</p>
            <p>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
