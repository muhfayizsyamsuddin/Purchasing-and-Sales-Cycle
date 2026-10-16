@extends('gentelella::page')

@section('title', 'Retur Penjualan')
@section('page_key', 'retur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Retur Penjualan')

@section('content')
    <div class="retur-penjualan-page">
        <x-gentelella::page-header
            title="Retur Penjualan"
            pretitle="Sales Cycle"
        >
            <x-slot:actions>
                @can('retur-penjualan.create')
                    <x-gentelella::btn
                        :href="route('retur-penjualan.create')"
                        variant="primary"
                    >
                        Tambah Retur
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Retur Penjualan">
            @if (session('success'))
                <div class="banner banner-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="banner banner-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <style>
                .retur-penjualan-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .retur-penjualan-table th,
                .retur-penjualan-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .retur-penjualan-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="retur-penjualan-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:8%">
                    <col style="width:7%">
                    <col style="width:8%">
                    <col style="width:8%">
                    <col style="width:9%">
                    <col style="width:9%">
                    <col style="width:6%">
                    <col style="width:6%">
                    <col style="width:9%">
                    <col style="width:12%">
                    <col style="width:18%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Retur</th>
                        <th>Tanggal</th>
                        <th>Tanggal Pengiriman</th>
                        <th>No. Pesanan</th>
                        <th>Nama Pembeli</th>
                        <th>Nama Penjual</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th>Total Kuantitas</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($returs as $retur)
                        <tr>
                            <td>{{ $retur->no_retur }}</td>
                            <td>{{ $retur->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                {{ $retur->pengirimanPesanan?->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                {{ $retur->pengirimanPesanan?->pesananPenjualan?->no_pesanan ?? '-' }}
                            </td>
                            <td>{{ $retur->nama_pembeli }}</td>
                            <td>{{ $retur->nama_penjual }}</td>
                            <td style="white-space:nowrap;">
                                {{ number_format((float) $retur->jumlah, 2, ',', '.') }}
                            </td>
                            <td style="white-space:nowrap;">
                                {{ number_format((float) $retur->ppn, 2, ',', '.') }}
                            </td>
                            <td style="white-space:nowrap;">
                                {{ number_format((float) $retur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>{{ $retur->details_sum_jumlah ?? 0 }}</td>
                            <td>
                                @can('retur-penjualan.view')
                                    <a
                                        href="{{ route('retur-penjualan.print', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                        rel="noopener"
                                    >Cetak</a>
                                @endcan

                                @can('retur-penjualan.update')
                                    <a
                                        href="{{ route('retur-penjualan.edit', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                @can('retur-penjualan.delete')
                                    <form
                                        action="{{ route('retur-penjualan.destroy', $retur->no_retur) }}"
                                        method="POST"
                                        style="display:inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline"
                                        >Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada retur penjualan
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data retur penjualan yang dibuat akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $returs->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection