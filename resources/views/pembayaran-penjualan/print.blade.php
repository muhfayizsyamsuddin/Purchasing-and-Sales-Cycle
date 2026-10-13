<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bukti Pembayaran - {{ $pembayaran->no_pembayaran }}</title>

    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #222;
            background: #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
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
            padding: 20mm;
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
            font-size: 19px;
        }

        .document-title {
            margin: 0 0 5px;
            font-size: 21px;
            text-align: right;
        }

        .muted {
            color: #64748b;
        }

        .section-title {
            margin: 22px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 14px;
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

        .payment-amount {
            margin-top: 24px;
            padding: 18px;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #159b83;
            border-radius: 3px;
        }

        .payment-amount .label {
            color: #64748b;
            font-size: 12px;
        }

        .payment-amount .amount {
            margin-top: 4px;
            font-size: 25px;
            font-weight: 700;
        }

        .terbilang {
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }

        .signature {
            width: 240px;
            max-width: 45%;
            margin: 48px 0 0 auto;
            text-align: center;
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

        @media print {
            body {
                background: #fff;
                font-size: 12px;
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

            .payment-amount,
            .terbilang,
            .signature {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a
            href="{{ route('pembayaran-penjualan.index') }}"
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
                <div class="muted">Dokumen Transaksi Penjualan</div>
            </div>

            <div>
                <h2 class="document-title">BUKTI PEMBAYARAN</h2>
                <div style="text-align: right">
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
                    <td>
                        <strong>{{ $pembayaran->no_pembayaran }}</strong>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ $pembayaran->tanggal->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Terima Dari</td>
                    <td>{{ $pembayaran->terima_dari }}</td>
                </tr>
                <tr>
                    <td>No. Faktur Penjualan</td>
                    <td>{{ $pembayaran->no_faktur }}</td>
                </tr>
                <tr>
                    <td>Nama pada Faktur</td>
                    <td>
                        {{ $pembayaran->fakturPenjualan?->nama ?? '-' }}
                    </td>
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
            <div>{{ $pembayaran->terbilang }}</div>
        </section>

        <div class="signature">
            <div>Diterima / Diperiksa oleh,</div>
            <div class="signature-space"></div>
            <div class="signature-line">
                ( ____________________ )
            </div>
        </div>

        <footer class="footer">
            Dicetak dari sistem Purchasing &amp; Sales Cycle
            pada {{ now()->format('d/m/Y H:i') }}.
        </footer>
    </main>
</body>
</html>