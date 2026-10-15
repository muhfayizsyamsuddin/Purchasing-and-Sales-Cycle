
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bukti Pembayaran - {{ $pembayaran->no_pembayaran }}</title>

    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #222;
            background: #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .toolbar {
            width: 210mm;
            max-width: 100%;
            margin: 20px auto 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 4px;
            padding: 9px 14px;
            color: #fff;
            background: #159b83;
            text-decoration: none;
            font-size: 12px;
            cursor: pointer;
        }

        .button-secondary {
            background: #475569;
        }

        .sheet {
            width: 210mm;
            min-height: 180mm;
            margin: 0 auto 24px;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 2px 12px rgb(0 0 0 / 10%);
        }

        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #159b83;
        }

        .company-name {
            margin: 0 0 5px;
            font-size: 20px;
            font-weight: 700;
        }

        .document-title {
            margin: 0 0 4px;
            font-size: 22px;
            text-align: right;
        }

        .muted {
            color: #64748b;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            margin: 22px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 13px;
            font-weight: 700;
        }

        .information-table {
            width: 100%;
            border-collapse: collapse;
        }

        .information-table td {
            padding: 6px 4px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .information-table td:first-child {
            width: 190px;
            color: #64748b;
        }

        .information-table td:nth-child(2) {
            padding-left: 8px;
            font-weight: 600;
        }

        .payment-amount {
            margin-top: 24px;
            padding: 18px;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #159b83;
            border-radius: 3px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .payment-amount .label {
            color: #64748b;
            font-size: 12px;
        }

        .payment-amount .amount {
            margin-top: 4px;
            color: #17212f;
            font-size: 25px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .terbilang {
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            overflow-wrap: anywhere;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .terbilang-text {
            margin-top: 5px;
            color: #334155;
        }

        .signature {
            width: 240px;
            max-width: 45%;
            margin: 48px 0 0 auto;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 75px;
        }

        .signature-line {
            padding-top: 6px;
            border-top: 1px solid #475569;
        }

        .footer {
            margin-top: 28px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 10px;
            text-align: center;
        }

        @media screen and (max-width: 760px) {
            .sheet {
                width: calc(100% - 24px);
                min-height: 0;
                margin: 0 12px 24px;
                padding: 24px;
            }

            .toolbar {
                width: calc(100% - 24px);
            }

            .document-header {
                gap: 12px;
            }

            .company-name {
                font-size: 15px;
            }

            .document-title {
                font-size: 17px;
            }

            .information-table td:first-child {
                width: 145px;
            }

            .payment-amount .amount {
                font-size: 21px;
            }
        }

        @media print {
            body {
                background: #fff;
                font-size: 11px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar {
                display: none !important;
            }

            .sheet {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .payment-amount {
                border-left-color: #159b83;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .payment-amount,
            .terbilang,
            .signature {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a
            href="{{ route('pembayaran-pembelian.index') }}"
            class="button button-secondary"
        >
            Kembali
        </a>

        <button
            type="button"
            class="button"
            onclick="window.print()"
        >
            Cetak Bukti Pembayaran
        </button>
    </div>

    <main class="sheet">
        <header class="document-header">
            <div>
                <h1 class="company-name">Purchasing &amp; Sales Cycle</h1>
                <div class="muted">Dokumen Transaksi Pembelian</div>
            </div>

            <div>
                <h2 class="document-title">BUKTI PEMBAYARAN</h2>
                <div class="text-right">
                    No.:
                    <strong>{{ $pembayaran->no_pembayaran }}</strong>
                </div>
            </div>
        </header>

        <section>
            <h3 class="section-title">Informasi Pembayaran</h3>

            <table class="information-table">
                <tr>
                    <td>No. Pembayaran</td>
                    <td>{{ $pembayaran->no_pembayaran }}</td>
                </tr>
                <tr>
                    <td>Tanggal Pembayaran</td>
                    <td>{{ $pembayaran->tanggal->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Terima Dari</td>
                    <td>{{ $pembayaran->terima_dari }}</td>
                </tr>
                <tr>
                    <td>No. Faktur</td>
                    <td>{{ $pembayaran->no_faktur }}</td>
                </tr>
                <tr>
                    <td>Nama pada Faktur</td>
                    <td>{{ $pembayaran->faktur?->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Untuk Pembayaran</td>
                    <td>{{ $pembayaran->untuk_pembayaran }}</td>
                </tr>
            </table>
        </section>

        <section class="payment-amount">
            <div class="label">Jumlah Pembayaran</div>
            <div class="amount">
                Rp {{ number_format((float) $pembayaran->jumlah, 2, ',', '.') }}
            </div>
        </section>

        <section class="terbilang">
            <strong>Terbilang:</strong>
            <div class="terbilang-text">{{ $pembayaran->terbilang }}</div>
        </section>

        <div class="signature">
            <div>Diterima / Diperiksa oleh,</div>
            <div class="signature-space"></div>
            <div class="signature-line">( ____________________ )</div>
        </div>

        <footer class="footer">
            Dicetak dari sistem Purchasing &amp; Sales Cycle
            pada {{ now()->format('d/m/Y H:i') }}.
        </footer>
    </main>
</body>
</html>
