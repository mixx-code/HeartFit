<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Order - {{ $order->order_number }}</title>
    <style>
        @page { margin: 15px 20px; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.3; color: #333; margin: 0; }
        h2 { margin: 0 0 2px 0; font-size: 16px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
        .header p { margin: 2px 0; font-size: 10px; color: #777; }
        .section { margin-bottom: 8px; }
        .section-title { font-size: 12px; font-weight: bold; color: #2c3e50; background: #f0f0f0; padding: 3px 6px; margin-bottom: 4px; }
        table.info { width: 100%; border-collapse: collapse; }
        table.info td { padding: 2px 6px; vertical-align: top; }
        table.info td.lbl { font-weight: bold; color: #555; width: 120px; white-space: nowrap; }
        table.two-col { width: 100%; border-collapse: collapse; }
        table.two-col > tbody > tr > td { width: 50%; vertical-align: top; padding: 0 4px 0 0; }
        table.two-col > tbody > tr > td:last-child { padding: 0 0 0 4px; }
        .badge { display: inline-block; padding: 1px 6px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-unpaid { background: #fff3cd; color: #856404; }
        .badge-expired { background: #e2e3e5; color: #383d41; }
        .badge-canceled { background: #f8d7da; color: #721c24; }
        .total-box { background: #e8f4fd; padding: 6px 8px; margin-top: 4px; }
        .total-box .amt { font-size: 14px; font-weight: bold; color: #2c3e50; }
        .notes-box { background: #fff8e1; border-left: 3px solid #ffc107; padding: 4px 8px; margin-top: 4px; font-style: italic; }
        .dates-inline { font-size: 10px; color: #555; }
        .footer { text-align: center; color: #999; font-size: 9px; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>HEARTFIT NUTRITION</h2>
        <p>Jasa Catering Makanan Sehat &mdash; Detail Order Customer</p>
    </div>

    @php
        $status = strtoupper($order->status ?? '-');
        $badgeClass = match($status) {
            'PAID', 'SETTLEMENT' => 'badge-paid',
            'UNPAID' => 'badge-unpaid',
            'EXPIRED' => 'badge-expired',
            'CANCELED' => 'badge-canceled',
            default => '',
        };
    @endphp

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Informasi Order</div>
                    <table class="info">
                        <tr><td class="lbl">No. Order</td><td>{{ $order->order_number }}</td></tr>
                        <tr><td class="lbl">Status</td><td><span class="badge {{ $badgeClass }}">{{ $status }}</span></td></tr>
                        <tr><td class="lbl">Metode</td><td>{{ strtoupper($order->payment_method ?? '-') }}</td></tr>
                        <tr><td class="lbl">Tanggal Order</td><td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><td class="lbl">Tanggal Bayar</td><td>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-' }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Informasi Customer</div>
                    <table class="info">
                        <tr><td class="lbl">Nama</td><td>{{ $order->user?->name ?? '-' }}</td></tr>
                        <tr><td class="lbl">Email</td><td>{{ $order->user?->email ?? '-' }}</td></tr>
                        <tr><td class="lbl">WhatsApp</td><td>{{ $order->whatsapp ?? '-' }}</td></tr>
                        <tr><td class="lbl">Alamat</td><td>{{ $order->user?->detail?->alamat ?? '-' }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Informasi Paket</div>
                    <table class="info">
                        <tr><td class="lbl">Paket</td><td><strong>{{ $order->package_label ?? '-' }}</strong></td></tr>
                        <tr><td class="lbl">Kategori</td><td>{{ ucfirst($order->package_category ?? '-') }}</td></tr>
                        <tr><td class="lbl">Batch</td><td>{{ $order->package_batch ?? '-' }}</td></tr>
                        <tr><td class="lbl">Durasi</td><td>{{ $order->days ?? 0 }} hari</td></tr>
                        <tr><td class="lbl">Menu Unik</td><td>{{ $order->unique_menu_count ?? 0 }} menu</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Periode & Pembayaran</div>
                    <table class="info">
                        <tr><td class="lbl">Periode</td><td>{{ $order->start_date ? $order->start_date->format('d/m/Y') : '-' }} s/d {{ $order->end_date ? $order->end_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="lbl">Harga Paket</td><td>Rp {{ number_format($order->package_price ?? 0, 0, ',', '.') }}</td></tr>
                    </table>
                    <div class="total-box">
                        Total Bayar: <span class="amt">Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    @if($order->service_dates && count($order->service_dates) > 0)
    <div class="section">
        <div class="section-title">Tanggal Layanan ({{ count($order->service_dates) }} hari)</div>
        <div class="dates-inline">
            {{ implode(', ', array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'), array_slice($order->service_dates, 0, 30))) }}
            @if(count($order->service_dates) > 30)
                ... +{{ count($order->service_dates) - 30 }} lainnya
            @endif
        </div>
    </div>
    @endif

    @if(!empty($order->notes))
    <div class="section">
        <div class="section-title">Catatan Khusus</div>
        <div class="notes-box">{{ $order->notes }}</div>
    </div>
    @endif

    <div class="footer">
        Dicetak otomatis dari sistem HeartFit Nutrition &mdash; {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
