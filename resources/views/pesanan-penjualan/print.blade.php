<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pesanan Penjualan - {{ $pesanan->no_pesanan }}</title>

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
            width: 140px;
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
        }

        .details th {
            text-align: center;
        }

        .details th:nth-child(1) { width: 15%; }
        .details th:nth-child(2) { width: 23%; }
        .details th:nth-child(3) { width: 13%; }
        .details th:nth-child(4) { width: 17%; }
        .details th:nth-child(5) { width: 13%; }
        .details th:nth-child(6) { width: 19%; }

        .numeric {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .summary {
            width: 55%;
            margin: 0 0 0 auto;
            border-collapse: collapse;
        }

        .summary td {
            padding: 6px;
            border: 1px solid #333;
        }

        .summary td:last-child {
            text-align: right;
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
        <a href="{{ route('pesanan-penjualan.index') }}">Kembali</a>

        <button type="button" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    <main class="paper">
        <h1>Pesanan Penjualan</h1>
        <div class="subtitle">Sales Cycle</div>

        <table class="metadata">
            <tr>
                <td>No. Pesanan</td>
                <td>:</td>
                <td>{{ $pesanan->no_pesanan }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ $pesanan->tanggal->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Nama Pembeli</td>
                <td>:</td>
                <td>{{ $pesanan->nama_pembeli }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pesanan->alamat }}</td>
            </tr>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kuantitas</th>
                    <th>Harga Unit</th>
                    <th>Diskon</th>
                    <th>Total Harga</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pesanan->details as $detail)
                    <tr>
                        <td>{{ $detail->sku }}</td>
                        <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                        <td class="center">{{ $detail->kuantitas }}</td>
                        <td class="numeric">
                            {{ number_format((float) $detail->harga_unit, 2, ',', '.') }}
                        </td>
                        <td class="numeric">
                            {{ number_format((float) $detail->diskon, 2, ',', '.') }}
                        </td>
                        <td class="numeric">
                            {{ number_format((float) $detail->total_harga, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center">
                            Belum ada detail barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>Jumlah</td>
                <td>
                    {{ number_format((float) $pesanan->jumlah, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>PPN</td>
                <td>
                    {{ number_format((float) $pesanan->ppn, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td><strong>Total Harga</strong></td>
                <td>
                    <strong>
                        {{ number_format((float) $pesanan->total_harga, 2, ',', '.') }}
                    </strong>
                </td>
            </tr>
        </table>

        <div class="signatures">
            <div class="signature">
                <div>Pembeli</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    {{ $pesanan->nama_pembeli }}
                </div>
            </div>

            <div class="signature">
                <div>Penjual</div>
                <div class="signature-space"></div>
                <div class="signature-line">(....................................)</div>
            </div>
        </div>
    </main>
</body>
</html>