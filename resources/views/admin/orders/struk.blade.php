<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            font-family: 'Courier New', Courier, monospace;
        }

        .struk {
            width: 80mm;
            background: #fff;
            padding: 10mm 5mm;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .struk-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .struk-header .logo {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .struk-header .subtitle {
            font-size: 11px;
            margin-top: 2px;
        }

        .struk-body {
            font-size: 12px;
            line-height: 1.6;
        }

        .struk-body .row {
            display: flex;
        }

        .struk-body .label {
            width: 80px;
            flex-shrink: 0;
            font-weight: bold;
        }

        .struk-body .sep {
            width: 10px;
            text-align: center;
            flex-shrink: 0;
        }

        .struk-body .value {
            flex: 1;
            word-break: break-word;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .struk-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
            border-top: 2px dashed #000;
            padding-top: 6px;
        }

        .struk-footer .warning {
            font-weight: bold;
            font-size: 11px;
        }

        .no-print {
            margin-bottom: 15px;
        }

        .btn-print {
            background: #333;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            font-family: sans-serif;
        }

        .btn-print:hover {
            background: #555;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .struk {
                box-shadow: none;
                width: 100%;
                padding: 5mm;
            }

            @page {
                size: A5;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    </div>

    <div class="struk">
        <div class="struk-header">
            <div class="logo">HEARTFIT</div>
            <div class="subtitle">ETIKET MAKAN PELANGGAN</div>
        </div>

        <div class="struk-body">
            <div class="row">
                <span class="label">TGL</span>
                <span class="sep">:</span>
                <span class="value">{{ now()->format('d-m-Y H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">NO. ORDER</span>
                <span class="sep">:</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>

            <hr class="divider">

            <div class="row">
                <span class="label">NAMA</span>
                <span class="sep">:</span>
                <span class="value">{{ strtoupper($order->user?->name ?? '-') }}</span>
            </div>
            <div class="row">
                <span class="label">WHATSAPP</span>
                <span class="sep">:</span>
                <span class="value">{{ $order->whatsapp ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">ALAMAT</span>
                <span class="sep">:</span>
                <span class="value">{{ $order->user?->detail?->alamat ?? '-' }}</span>
            </div>

            <hr class="divider">

            <div class="row">
                <span class="label">PAKET</span>
                <span class="sep">:</span>
                <span class="value">{{ strtoupper($order->package_label) }}</span>
            </div>
            <div class="row">
                <span class="label">KATEGORI</span>
                <span class="sep">:</span>
                <span class="value">{{ strtoupper($order->package_category ?? '-') }}</span>
            </div>
            <div class="row">
                <span class="label">PERIODE</span>
                <span class="sep">:</span>
                <span class="value">
                    {{ $order->start_date ? $order->start_date->format('d/m/Y') : '-' }}
                    s.d
                    {{ $order->end_date ? $order->end_date->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="row">
                <span class="label">DURASI</span>
                <span class="sep">:</span>
                <span class="value">{{ $order->days ?? 0 }} HARI</span>
            </div>
            <div class="row">
                <span class="label">TOTAL</span>
                <span class="sep">:</span>
                <span class="value">Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span class="label">STATUS</span>
                <span class="sep">:</span>
                <span class="value">{{ strtoupper($order->status ?? '-') }}</span>
            </div>

            <hr class="divider">

            <div class="row">
                <span class="label">CATATAN</span>
                <span class="sep">:</span>
                <span class="value">{{ $order->notes ?? '-' }}</span>
            </div>
        </div>

        <div class="struk-footer">
            <div class="warning">Di konsumsi Max 1 Jam setelah di hidangkan</div>
            <div style="margin-top: 4px;">Terima kasih telah memilih HeartFit</div>
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

</body>
</html>
