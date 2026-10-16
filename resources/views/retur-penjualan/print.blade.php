
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Retur Penjualan - {{ $retur->no_retur }}</title>

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
            min-height: 250mm;
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
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
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
            padding: 5px 0;
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
            padding: 7px 5px;
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

        .number {
            text-align: right !important;
            white-space: nowrap;
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

        .note {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            overflow-wrap: anywhere;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 48px;
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
            overflow-wrap: anywhere;
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

            .information-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .detail-table th,
            .detail-table td {
                padding: 5px 3px;
                font-size: 10px;
            }
        }

        @media print {
            body {
                background: #fff;
                font-size: 10px;
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

            .note,
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
            href="{{ route('retur-penjualan.index') }}"
            class="button button-secondary"
        >
            Kembali
        </a>

        <button
            type="button"
            class="button"
            onclick="window.print()"
        >
            Cetak Retur
        </button>
    </div>

    <main class="sheet">
        <header class="document-header">
            <div>
                <h1 class="company-name">Purchasing &amp; Sales Cycle</h1>
                <div class="muted">Dokumen Transaksi Penjualan</div>
            </div>

            <div>
                <h2 class="document-title">RETUR PENJUALAN</h2>
                <div class="text-right">
                    No. Retur:
                    <strong>{{ $retur->no_retur }}</strong>
                </div>
            </div>
        </header>

        <section>
            <h3 class="section-title">Informasi Retur</h3>

            <div class="information-grid">
                <table class="information-table">
                    <tr>
                        <td>No. Retur</td>
                        <td>{{ $retur->no_retur }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>{{ $retur->tanggal->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>No. Pengiriman</td>
                        <td>
                            {{ $retur->pengirimanPesanan
                                ? 'PG-' . \Carbon\Carbon::parse($retur->pengirimanPesanan->tanggal)->format('Y')
                                    . '-' . str_pad(
                                        (string) $retur->id_pengiriman_pesanan,
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    )
                                : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>No. Pesanan</td>
                        <td>
                            {{ $retur->pengirimanPesanan?->pesananPenjualan?->no_pesanan ?? '-' }}
                        </td>
                    </tr>
                </table>

                <table class="information-table">
                    <tr>
                        <td>Nama Pembeli</td>
                        <td>{{ $retur->nama_pembeli }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Pembeli</td>
                        <td>{{ $retur->alamat_pembeli }}</td>
                    </tr>
                    <tr>
                        <td>Nama Penjual</td>
                        <td>{{ $retur->nama_penjual }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Penjual</td>
                        <td>{{ $retur->alamat_penjual }}</td>
                    </tr>
                </table>
            </div>
        </section>

        <section>
            <h3 class="section-title">Rincian Barang Retur</h3>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 6%">No.</th>
                        <th style="width: 12%">SKU</th>
                        <th style="width: 24%">Nama Barang</th>
                        <th style="width: 10%" class="number">Jumlah</th>
                        <th style="width: 16%" class="number">Harga Unit</th>
                        <th style="width: 12%" class="number">Diskon</th>
                        <th style="width: 20%" class="number">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($retur->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->sku }}</td>
                            <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                            <td class="number">
                                {{ number_format((int) $detail->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="number">
                                {{ number_format((float) $detail->harga_unit, 2, ',', '.') }}
                            </td>
                            <td class="number">
                                {{ number_format((float) $detail->diskon, 2, ',', '.') }}
                            </td>
                            <td class="number">
                                {{ number_format((float) $detail->total_harga, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Tidak ada detail barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <table class="totals">
            <tbody>
                <tr>
                    <td>Subtotal setelah diskon</td>
                    <td>
                        Rp {{ number_format((float) $retur->jumlah, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>PPN</td>
                    <td>
                        Rp {{ number_format((float) $retur->ppn, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="grand-total">
                    <td>Total Retur</td>
                    <td>
                        Rp {{ number_format((float) $retur->total_harga, 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="note">
            <strong>Keterangan:</strong>
            Dokumen ini mencatat barang yang dikembalikan berdasarkan
            pengiriman pesanan yang tercantum di atas.
        </div>

        <div class="signatures">
            <div>
                <div>Pembeli,</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    {{ $retur->nama_pembeli }}
                </div>
            </div>

            <div>
                <div>Penjual,</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    {{ $retur->nama_penjual }}
                </div>
            </div>
        </div>

        <footer class="footer">
            Dicetak dari sistem Purchasing &amp; Sales Cycle
            pada {{ now()->format('d/m/Y H:i') }}.
        </footer>
    </main>
</body>
</html>
