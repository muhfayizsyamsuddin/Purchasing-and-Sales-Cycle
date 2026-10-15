
@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')
@section('breadcrumb', 'Home > Dashboard')

@section('content')
    <x-gentelella::page-header
        title="Dashboard"
        pretitle="OVERVIEW"
    />

    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 6px;">
            Purchasing &amp; Sales Overview
        </h2>
        <p style="color: var(--text-muted); margin: 0;">
            Ringkasan data master dan aktivitas operasional.
        </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:24px;">
        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Total Barang</div>
                    <div style="font-size:28px;font-weight:700;">{{ number_format($totalBarang) }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">Data inventaris</div>
                </div>
                <div style="font-size:26px;color:#2dd4bf;">▦</div>
            </div>
        </x-gentelella::card>

        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Total Vendor</div>
                    <div style="font-size:28px;font-weight:700;">{{ number_format($totalVendor) }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">Data pemasok</div>
                </div>
                <div style="font-size:26px;color:#60a5fa;">♙</div>
            </div>
        </x-gentelella::card>

        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Pesanan Pembelian</div>
                    <div style="font-size:28px;font-weight:700;">{{ number_format($totalPesananPembelian) }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">Transaksi purchasing</div>
                </div>
                <div style="font-size:26px;color:#fbbf24;">⇄</div>
            </div>
        </x-gentelella::card>

        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Faktur Penjualan</div>
                    <div style="font-size:28px;font-weight:700;">{{ number_format($totalFakturPenjualan) }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">Transaksi sales</div>
                </div>
                <div style="font-size:26px;color:#c084fc;">▤</div>
            </div>
        </x-gentelella::card>

        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;min-height:76px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">
                        Nilai Pesanan Pembelian
                    </div>
                    <div style="font-size:28px;font-weight:700;color:#2dd4bf;">
                        Rp {{ number_format((float) $nilaiPembelian, 0, ',', '.') }}
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">
                        Total nilai seluruh pesanan pembelian
                    </div>
                </div>
                <div style="font-size:26px;color:#2dd4bf;">⇄</div>
            </div>
        </x-gentelella::card>

        <x-gentelella::card>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;min-height:76px;">
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">
                        Nilai Pesanan Penjualan
                    </div>
                    <div style="font-size:28px;font-weight:700;color:#60a5fa;">
                        Rp {{ number_format((float) $nilaiPenjualan, 0, ',', '.') }}
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">
                        Total nilai seluruh pesanan penjualan
                    </div>
                </div>
                <div style="font-size:26px;color:#60a5fa;">⇄</div>
            </div>
        </x-gentelella::card>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-top:24px;">

        <x-gentelella::card title="Transaksi Pembelian Terbaru">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left;border-bottom:1px solid var(--border-color);">
                            <th style="padding:10px 8px;">No. Faktur</th>
                            <th style="padding:10px 8px;">Tanggal</th>
                            <th style="padding:10px 8px;text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiPembelianTerbaru as $transaksi)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:10px 8px;">{{ $transaksi->no_faktur }}</td>
                                <td style="padding:10px 8px;">{{ $transaksi->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                <td style="padding:10px 8px;text-align:right;white-space:nowrap;">
                                    Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding:16px 8px;text-align:center;color:var(--text-muted);">
                                    Belum ada transaksi pembelian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella::card>

        <x-gentelella::card title="Transaksi Penjualan Terbaru">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left;border-bottom:1px solid var(--border-color);">
                            <th style="padding:10px 8px;">No. Faktur</th>
                            <th style="padding:10px 8px;">Tanggal</th>
                            <th style="padding:10px 8px;text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiPenjualanTerbaru as $transaksi)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:10px 8px;">{{ $transaksi->no_faktur }}</td>
                                <td style="padding:10px 8px;">{{ $transaksi->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                <td style="padding:10px 8px;text-align:right;white-space:nowrap;">
                                    Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding:16px 8px;text-align:center;color:var(--text-muted);">
                                    Belum ada transaksi penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-gentelella::card>

    </div>
@endsection
