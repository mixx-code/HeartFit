<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Orders{{ $q ? ' - Pencarian: ' . $q : '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 12px;
            color: #555;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #f0f0f0;
            border: 1px solid #aaa;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
            white-space: nowrap;
        }

        tbody td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            vertical-align: top;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .badge-danger { background: #f8d7da; color: #721c24; }

        .summary {
            margin-top: 15px;
            font-size: 12px;
        }

        .summary table {
            width: auto;
            margin-top: 5px;
        }

        .summary td {
            padding: 3px 12px 3px 0;
            border: none;
        }

        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 999;
        }

        .btn-print {
            background: #0d6efd;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #0b5ed7;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 10px;
            }

            @page {
                size: landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print / PDF</button>
    </div>

    <div class="header">
        <h1>Laporan Data Orders</h1>
        <p>HeartFit — Dicetak pada {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
        @if(!empty($dateFrom) || !empty($dateTo))
            <p style="margin-top:4px;">Periode: <strong>{{ $dateFrom ?? '...' }} s/d {{ $dateTo ?? '...' }}</strong></p>
        @endif
        @if($q)
            <p style="margin-top:4px;">Filter pencarian: <strong>{{ $q }}</strong></p>
        @endif
    </div>

    <div class="meta">
        <span>Total data: <strong>{{ $orders->count() }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Order</th>
                <th>Nama User</th>
                <th>Email</th>
                <th>Paket</th>
                <th>Kategori</th>
                <th>Periode</th>
                <th>Hari</th>
                <th class="text-right">Total (Rp)</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Paid At</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($orders as $i => $o)
                @php
                    $total = $o->amount_total ?? $o->package_price;
                    $grandTotal += (int) $total;
                    $status = strtoupper($o->status ?? '');
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $o->order_number }}</td>
                    <td>{{ $o->user?->name ?? '—' }}</td>
                    <td>{{ $o->user?->email ?? '—' }}</td>
                    <td>{{ $o->package_label }}</td>
                    <td>{{ $o->package_category ?? '—' }}</td>
                    <td>
                        {{ $o->start_date ? $o->start_date->format('Y-m-d') : '—' }}
                        —
                        {{ $o->end_date ? $o->end_date->format('Y-m-d') : '—' }}
                    </td>
                    <td class="text-center">{{ (int) ($o->days ?? 0) }}</td>
                    <td class="text-right">{{ number_format((int) $total, 0, ',', '.') }}</td>
                    <td class="text-uppercase">{{ str_replace('_', ' ', (string) $o->payment_method) }}</td>
                    <td class="text-center">
                        @switch($status)
                            @case('PAID')
                            @case('SETTLEMENT')
                                <span class="badge badge-success">PAID</span>
                            @break
                            @case('UNPAID')
                                <span class="badge badge-warning">UNPAID</span>
                            @break
                            @case('EXPIRED')
                                <span class="badge badge-secondary">EXPIRED</span>
                            @break
                            @case('CANCELED')
                                <span class="badge badge-danger">CANCELED</span>
                            @break
                            @default
                                <span class="badge">{{ $status ?: '—' }}</span>
                        @endswitch
                    </td>
                    <td>{{ $o->paid_at ? $o->paid_at->format('Y-m-d H:i') : '—' }}</td>
                    <td>{{ $o->created_at ? $o->created_at->format('Y-m-d H:i') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Grand Total</th>
                <th class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="summary">
        <strong>Ringkasan Status:</strong>
        <table>
            <tr>
                <td>Paid/Settlement</td>
                <td>: {{ $orders->filter(fn($o) => in_array(strtoupper($o->status), ['PAID','SETTLEMENT']))->count() }}</td>
            </tr>
            <tr>
                <td>Unpaid</td>
                <td>: {{ $orders->filter(fn($o) => strtoupper($o->status) === 'UNPAID')->count() }}</td>
            </tr>
            <tr>
                <td>Expired</td>
                <td>: {{ $orders->filter(fn($o) => strtoupper($o->status) === 'EXPIRED')->count() }}</td>
            </tr>
            <tr>
                <td>Canceled</td>
                <td>: {{ $orders->filter(fn($o) => strtoupper($o->status) === 'CANCELED')->count() }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
