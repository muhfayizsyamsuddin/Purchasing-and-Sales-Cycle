
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pembelian - {{ $permintaan->no_faktur }}</title>

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
            padding: 9px 7px;
            border: 1px solid #cbd5e1;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .detail-table th {
            background: #f1f5f9;
            font-weight: 700;
        }

        .text-right {
            text-align: right !important;
            white-space: nowrap;
        }

        .text-center {
            text-align: center !important;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
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

            .detail-table tr {
                page-break-inside: avoid;
            }

            .detail-table thead {
                display: table-header-group;
            }

            .signatures {
                page-break-inside: avoid;
            }

            .detail-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a
            href="{{ route('permintaan-pembelian.index') }}"
            class="button button-secondary"
        >
            Kembali
        </a>

        <button
            type="button"
            class="button"
            onclick="window.print()"
        >
            Cetak Permintaan
        </button>
    </div>

    <main class="sheet">
        <header class="document-header">
            <div>
                <h1 class="company-name">Purchasing &amp; Sales Cycle</h1>
                <div class="muted">Dokumen Transaksi Pembelian</div>
            </div>

            <div>
                <h2 class="document-title">PERMINTAAN PEMBELIAN</h2>
                <div class="text-right">
                    No. Faktur:
                    <strong>{{ $permintaan->no_faktur }}</strong>
                </div>
            </div>
        </header>

        <section>
            <h3 class="section-title">Informasi Permintaan</h3>

            <div class="information-grid">
                <table class="information-table">
                    <tr>
                        <td>Tanggal Permintaan</td>
                        <td>
                            {{ \Illuminate\Support\Carbon::parse($permintaan->tanggal)->format('d/m/Y') }}
                        </td>
                    </tr>
                </table>

                <table class="information-table">
                    <tr>
                        <td>Tanggal Diperlukan</td>
                        <td>
                            {{ \Illuminate\Support\Carbon::parse($permintaan->tanggal_diperlukan)->format('d/m/Y') }}
                        </td>
                    </tr>
                </table>
            </div>
        </section>

        <section>
            <h3 class="section-title">Rincian Barang</h3>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 18%">SKU</th>
                        <th style="width: 32%">Nama Barang</th>
                        <th class="text-center" style="width: 15%">Kuantitas</th>
                        <th style="width: 35%">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($permintaan->details as $detail)
                        <tr>
                            <td>{{ $detail->sku }}</td>
                            <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                            <td class="text-center">
                                {{ number_format($detail->kuantitas, 0, ',', '.') }}
                            </td>
                            <td>{{ $detail->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Belum ada detail barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="signatures">
            <div>
                <div>Dibuat oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line">( ____________________ )</div>
            </div>

            <div>
                <div>Diperiksa / Disetujui oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line">( ____________________ )</div>
            </div>
        </div>

        <footer class="footer">
            Dicetak dari sistem Purchasing &amp; Sales Cycle
            pada {{ now()->format('d/m/Y H:i') }}.
        </footer>
    </main>
</body>
</html>
