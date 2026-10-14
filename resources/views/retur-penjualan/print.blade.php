@extends('gentelella::page')

@section('title', 'Cetak Retur Penjualan')
@section('page_key', 'retur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Retur Penjualan > Cetak')

@section('content')
    <style>
        .return-document {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px;
            background: #fff;
            color: #222;
        }

        .return-document .document-header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
            margin-bottom: 24px;
        }

        .return-document h2,
        .return-document h3,
        .return-document p {
            margin-top: 0;
        }

        .return-document .metadata {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        .return-document table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .return-document th,
        .return-document td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .return-document th {
            background: #f2f2f2;
        }

        .return-document .number {
            text-align: right;
            white-space: nowrap;
        }

        .return-document .totals {
            width: 340px;
            max-width: 100%;
            margin-left: auto;
        }

        .return-document .totals td {
            border: none;
            border-bottom: 1px solid #ddd;
        }

        .return-document .grand-total {
            font-weight: bold;
            font-size: 1.05rem;
        }

        .return-document .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            margin-top: 64px;
            text-align: center;
        }

        .return-document .signature-space {
            height: 80px;
        }

        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                background: #fff !important;
            }

            .no-print,
            nav,
            .sidebar-footer,
            footer {
                display: none !important;
            }

            .return-document {
                max-width: none;
                padding: 0;
                margin: 0;
            }

            .return-document th {
                background: #eee !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }

        @media (max-width: 600px) {
            .return-document {
                padding: 16px;
            }

            .return-document .document-header,
            .return-document .metadata {
                display: block;
            }

            .return-document .metadata > div + div {
                margin-top: 20px;
            }
        }
    </style>

    <div class="no-print" style="max-width: 900px; margin: 0 auto 16px">
        <button
            type="button"
            class="btn btn-primary"
            onclick="window.print()"
        >
            Cetak / Simpan PDF
        </button>

        <a
            href="{{ route('retur-penjualan.index') }}"
            class="btn btn-outline"
        >
            Kembali
        </a>
    </div>

    <div class="return-document">
        <div class="document-header">
            <div>
                <h2>RETUR PENJUALAN</h2>
                <p>Dokumen pengembalian barang dari pembeli</p>
            </div>

            <div style="text-align: right">
                <h3>{{ $retur->no_retur }}</h3>
                <p>
                    Tanggal:
                    {{ $retur->tanggal->format('d/m/Y') }}
                </p>
                <p>
                    ID Pengiriman:
                    {{ $retur->id_pengiriman_pesanan }}
                </p>
                <p>
                    No. Pesanan:
                    {{ $retur->pengirimanPesanan?->pesananPenjualan?->no_pesanan ?? '-' }}
                </p>
            </div>
        </div>

        <div class="metadata">
            <div>
                <h4>Informasi Pembeli</h4>
                <p>
                    <strong>{{ $retur->nama_pembeli }}</strong>
                </p>
                <p>
                    {{ $retur->alamat_pembeli }}
                </p>
            </div>

            <div>
                <h4>Informasi Penjual</h4>
                <p>
                    <strong>{{ $retur->nama_penjual }}</strong>
                </p>
                <p>
                    {{ $retur->alamat_penjual }}
                </p>
            </div>
        </div>

        <h4>Rincian Barang Retur</h4>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th class="number">Jumlah</th>
                    <th class="number">Harga Unit</th>
                    <th class="number">Diskon</th>
                    <th class="number">Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($retur->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
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
                        <td colspan="7" style="text-align: center">
                            Tidak ada detail barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="totals">
            <tbody>
                <tr>
                    <td>Subtotal setelah diskon</td>
                    <td class="number">
                        Rp {{ number_format((float) $retur->jumlah, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>PPN</td>
                    <td class="number">
                        Rp {{ number_format((float) $retur->ppn, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="grand-total">
                    <td>Total Retur</td>
                    <td class="number">
                        Rp {{ number_format((float) $retur->total_harga, 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div>
                <p>Pembeli,</p>
                <div class="signature-space"></div>
                <strong>{{ $retur->nama_pembeli }}</strong>
            </div>

            <div>
                <p>Penjual,</p>
                <div class="signature-space"></div>
                <strong>{{ $retur->nama_penjual }}</strong>
            </div>
        </div>
    </div>
@endsection