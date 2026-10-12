<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penerimaan Barang - {{ $penerimaan->no_penerimaan }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f0f2f5;
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .toolbar {
            max-width: 210mm;
            margin: 16px auto;
            text-align: right;
        }

        .toolbar a,
        .toolbar button {
            padding: 9px 14px;
            border: 1px solid #bbb;
            border-radius: 4px;
            background: #fff;
            color: #111;
            text-decoration: none;
            cursor: pointer;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 20px;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 2px 10px #0001;
        }

        h1 {
            margin: 0 0 5px;
            text-align: center;
            font-size: 20px;
            text-transform: uppercase;
        }

        .subtitle {
            margin-bottom: 24px;
            text-align: center;
        }

        .metadata {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .metadata td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .metadata td:first-child {
            width: 170px;
        }

        .metadata td:nth-child(2) {
            width: 15px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .details th,
        .details td {
            padding: 7px 5px;
            border: 1px solid #333;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        .details th {
            text-align: center;
        }

        .details th:nth-child(1) { width: 15%; }
        .details th:nth-child(2) { width: 22%; }
        .details th:nth-child(3) { width: 13%; }
        .details th:nth-child(4) { width: 20%; }
        .details th:nth-child(5) { width: 30%; }

        .center {
            text-align: center;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 55px;
            text-align: center;
        }

        .signature {
            width: 42%;
        }

        .signature-space {
            height: 75px;
        }

        .signature-line {
            padding-top: 6px;
            border-top: 1px solid #555;
        }

        @media print {
            body {
                background: #fff;
            }

            .toolbar {
                display: none !important;
            }

            .paper {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .details thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a href="{{ route('penerimaan-barang.index') }}">Kembali</a>

        <button type="button" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    <main class="paper">
        <h1>Penerimaan Barang</h1>
        <div class="subtitle">Purchasing Cycle</div>

        <table class="metadata">
            <tr>
                <td>No. Penerimaan</td>
                <td>:</td>
                <td>{{ $penerimaan->no_penerimaan }}</td>
            </tr>
            <tr>
                <td>Tanggal Penerimaan</td>
                <td>:</td>
                <td>{{ $penerimaan->tanggal->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>No. Order Pembelian</td>
                <td>:</td>
                <td>{{ $penerimaan->pesanan?->no_order_pembelian ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Vendor</td>
                <td>:</td>
                <td>{{ $penerimaan->pesanan?->vendor?->nama_vendor ?? '-' }}</td>
            </tr>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kuantitas</th>
                    <th>Kondisi Barang</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($penerimaan->details as $detail)
                    <tr>
                        <td>{{ $detail->sku }}</td>
                        <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                        <td class="center">{{ $detail->kuantitas }}</td>
                        <td>{{ $detail->kondisi_barang }}</td>
                        <td>{{ $detail->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center">
                            Belum ada detail barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature">
                <div>Pihak Penerima</div>
                <div class="signature-space"></div>
                <div class="signature-line">Nama dan tanda tangan</div>
            </div>

            <div class="signature">
                <div>Pihak Perusahaan</div>
                <div class="signature-space"></div>
                <div class="signature-line">Nama dan tanda tangan</div>
            </div>
        </div>
    </main>
</body>
</html>