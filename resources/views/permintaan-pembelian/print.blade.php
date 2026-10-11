
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pembelian - {{ $permintaan->no_faktur }}</title>

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

        .toolbar button,
        .toolbar a {
            display: inline-block;
            padding: 9px 14px;
            border: 1px solid #bbb;
            border-radius: 4px;
            background: #fff;
            color: #111;
            text-decoration: none;
            cursor: pointer;
            font-size: 12px;
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
            margin-bottom: 28px;
            text-align: center;
            color: #444;
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
            width: 155px;
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
            border: 1px solid #333;
            padding: 7px 6px;
            overflow-wrap: anywhere;
        }

        .details th {
            text-align: center;
            font-weight: bold;
        }

        .details th:nth-child(1) {
            width: 17%;
        }

        .details th:nth-child(2) {
            width: 33%;
        }

        .details th:nth-child(3) {
            width: 15%;
        }

        .details th:nth-child(4) {
            width: 35%;
        }

        .quantity {
            text-align: center;
        }

        .blank-row td {
            height: 28px;
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

        @media screen {
            .paper {
                min-height: 297mm;
            }
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
        <a href="{{ route('permintaan-pembelian.index') }}">Kembali</a>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <main class="paper">
        <h1>Permintaan Pembelian</h1>
        <div class="subtitle">Purchasing Cycle</div>

        <table class="metadata">
            <tr>
                <td>No. Faktur</td>
                <td>:</td>
                <td>{{ $permintaan->no_faktur }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Illuminate\Support\Carbon::parse($permintaan->tanggal)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Diperlukan</td>
                <td>:</td>
                <td>{{ \Illuminate\Support\Carbon::parse($permintaan->tanggal_diperlukan)->format('d/m/Y') }}</td>
            </tr>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th>Kuantitas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($permintaan->details as $detail)
                    <tr>
                        <td>{{ $detail->sku }}</td>
                        <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                        <td class="quantity">{{ $detail->kuantitas }}</td>
                        <td>{{ $detail->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada detail barang.</td>
                    </tr>
                @endforelse

                @for ($i = 0; $i < max(0, 10 - $permintaan->details->count()); $i++)
                    <tr class="blank-row">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature">
                <div>Tanda tangan</div>
                <div class="signature-space"></div>
                <div class="signature-line">Pemohon</div>
            </div>

            <div class="signature">
                <div>Tanda tangan</div>
                <div class="signature-space"></div>
                <div class="signature-line">Menyetujui</div>
            </div>
        </div>
    </main>
</body>
</html>
