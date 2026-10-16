
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faktur Penjualan - {{ $faktur->no_faktur }}</title>

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
            min-height: 297mm;
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

        .section-title {
            margin: 22px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 13px;
            font-weight: 700;
        }

        .information-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .information-table {
            width: 100%;
            border-collapse: collapse;
        }

        .information-table td {
            padding: 4px 0;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .information-table td:first-child {
            width: 42%;
            color: #64748b;
        }

        .information-table td:nth-child(2) {
            padding-left: 8px;
            font-weight: 600;
        }

        .detail-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .detail-table th,
        .detail-table td {
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .detail-table th {
            background: #f1f5f9;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .totals {
            width: 300px;
            max-width: 100%;
            margin: 16px 0 0 auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 8px;
        }

        .totals td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .grand-total td {
            padding-top: 10px;
            border-top: 2px solid #159b83;
            font-size: 15px;
            font-weight: 700;
        }

        .payment-note {
            margin-top: 18px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            overflow-wrap: anywhere;
        }

        .payment-note div {
            margin-top: 4px;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 48px;
            text-align: center;
        }

        .signature-space {
            height: 75px;
        }

        .signature-line {
            padding-top: 6px;
            border-top: 1px solid #475569;
            overflow-wrap: anywhere;
        }

        .footer {
            margin-top: 30px;
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

            .information-grid {
                grid-template-columns: 1fr;
                gap: 0;
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

            .detail-table th,
            .detail-table td {
                padding: 5px 4px;
                font-size: 10px;
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

            .detail-table {
                page-break-inside: auto;
            }

            .detail-table thead {
                display: table-header-group;
            }

            .detail-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .payment-note,
            .signatures {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a
            href="{{ route('faktur-penjualan.index') }}"
            class="button button-secondary"
        >
            Kembali
        </a>

        <button
            type="button"
            class="button"
            onclick="window.print()"
        >
            Cetak Faktur
        </button>
    </div>

    <main class="sheet">
        <header class="document-header">
            <div>
                <h1 class="company-name">Purchasing &amp; Sales Cycle</h1>
                <div class="muted">Dokumen Transaksi Penjualan</div>
            </div>

            <div>
                <h2 class="document-title">FAKTUR PENJUALAN</h2>
                <div class="text-right">
                    No. Faktur:
                    <strong>{{ $faktur->no_faktur }}</strong>
                </div>
            </div>
        </header>

        <section>
            <h3 class="section-title">Informasi Faktur</h3>

            <div class="information-grid">
                <table class="information-table">
                    <tr>
                        <td>Tanggal Faktur</td>
                        <td>{{ $faktur->tanggal->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pembeli</td>
                        <td>{{ $faktur->nama }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td>{{ $faktur->no_telp }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>{{ $faktur->alamat }}</td>
                    </tr>
                </table>

                <table class="information-table">
                    <tr>
                        <td>No. Pengiriman</td>
                        <td>
                            {{ $faktur->pengirimanPesanan
                                ? 'PG-' . \Carbon\Carbon::parse($faktur->pengirimanPesanan->tanggal)->format('Y')
                                    . '-' . str_pad((string) $faktur->id_pengiriman_pesanan, 3, '0', STR_PAD_LEFT)
                                : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>No. Pesanan</td>
                        <td>
                            {{ $faktur->pengirimanPesanan?->no_pesanan ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Jangka Pembayaran</td>
                        <td>{{ $faktur->jangka_waktu_pembayaran }}</td>
                    </tr>
                </table>
            </div>
        </section>

        <section>
            <h3 class="section-title">Rincian Barang</h3>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 15%">SKU</th>
                        <th style="width: 25%">Nama Barang</th>
                        <th class="text-right" style="width: 12%">Kuantitas</th>
                        <th class="text-right" style="width: 16%">Harga Unit</th>
                        <th class="text-right" style="width: 14%">Diskon</th>
                        <th class="text-right" style="width: 18%">Total Harga</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($faktur->details as $detail)
                        <tr>
                            <td>{{ $detail->sku }}</td>
                            <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                            <td class="text-right">
                                {{ number_format($detail->kuantitas, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format((float) $detail->harga_unit, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format((float) $detail->diskon, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format((float) $detail->total_harga, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada detail barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td>Jumlah</td>
                    <td>
                        Rp {{ number_format((float) $faktur->jumlah, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>PPN</td>
                    <td>
                        Rp {{ number_format((float) $faktur->ppn, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="grand-total">
                    <td>Total Harga</td>
                    <td>
                        Rp {{ number_format((float) $faktur->total_harga, 2, ',', '.') }}
                    </td>
                </tr>
            </table>

            <div class="payment-note">
                <strong>Ketentuan Pembayaran</strong>
                <div>
                    Jangka waktu pembayaran:
                    {{ $faktur->jangka_waktu_pembayaran }}
                </div>
                <div>
                    Mohon mencantumkan nomor faktur
                    {{ $faktur->no_faktur }} saat melakukan pembayaran.
                </div>
            </div>
        </section>

        <div class="signatures">
            <div>
                <div>Pembeli</div>
                <div class="signature-space"></div>
                <div class="signature-line">{{ $faktur->nama }}</div>
            </div>

            <div>
                <div>Penjual</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    (....................................)
                </div>
            </div>
        </div>

        <footer class="footer">
            Dokumen Faktur Penjualan — {{ $faktur->no_faktur }}
        </footer>
    </main>
</body>
</html>
