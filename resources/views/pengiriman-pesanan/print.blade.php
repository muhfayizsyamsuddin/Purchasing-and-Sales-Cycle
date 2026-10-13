<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pengiriman Pesanan - {{ $pengiriman->id_pengiriman_pesanan }}</title>

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
            width: 150px;
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

        .details th:nth-child(1) { width: 18%; }
        .details th:nth-child(2) { width: 30%; }
        .details th:nth-child(3) { width: 20%; }
        .details th:nth-child(4) { width: 32%; }

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
        <a href="{{ route('pengiriman-pesanan.index') }}">Kembali</a>

        <button type="button" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    <main class="paper">
        <h1>Pengiriman Pesanan</h1>
        <div class="subtitle">Sales Cycle</div>

        <table class="metadata">
            <tr>
                <td>ID Pengiriman</td>
                <td>:</td>
                <td>{{ $pengiriman->id_pengiriman_pesanan }}</td>
            </tr>
            <tr>
                <td>No. Pesanan</td>
                <td>:</td>
                <td>{{ $pengiriman->no_pesanan }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengiriman</td>
                <td>:</td>
                <td>{{ $pengiriman->tanggal->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Nama Pembeli</td>
                <td>:</td>
                <td>{{ $pengiriman->pesananPenjualan?->nama_pembeli ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pengiriman->pesananPenjualan?->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Pengirim</td>
                <td>:</td>
                <td>{{ $pengiriman->nama_pengirim }}</td>
            </tr>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Dikirim</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pengiriman->details as $detail)
                    <tr>
                        <td>{{ $detail->sku }}</td>
                        <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                        <td class="center">{{ $detail->total }}</td>
                        <td>{{ $detail->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center">
                            Belum ada detail pengiriman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature">
                <div>Penerima</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    {{ $pengiriman->pesananPenjualan?->nama_pembeli ?? '-' }}
                </div>
            </div>

            <div class="signature">
                <div>Pengirim</div>
                <div class="signature-space"></div>
                <div class="signature-line">
                    {{ $pengiriman->nama_pengirim }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>