
@extends('gentelella::page')

@section('title', 'Pesanan Penjualan')
@section('page_key', 'pesanan-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pesanan Penjualan')

@section('content')
    <div class="pesanan-penjualan-page">
        <x-gentelella::page-header
            title="Pesanan Penjualan"
            pretitle="Sales Cycle"
        >
            <x-slot:actions>
                @can('pesanan-penjualan.create')
                    <x-gentelella::btn
                        :href="route('pesanan-penjualan.create')"
                        variant="primary"
                    >
                        Buat Pesanan
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Pesanan Penjualan">
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
                .pesanan-penjualan-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .pesanan-penjualan-table th,
                .pesanan-penjualan-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pesanan-penjualan-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="pesanan-penjualan-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:15%">
                    <col style="width:11%">
                    <col style="width:18%">
                    <col style="width:10%">
                    <col style="width:9%">
                    <col style="width:16%">
                    <col style="width:21%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->no_pesanan }}</td>
                            <td>{{ $pesanan->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pesanan->nama_pembeli }}</td>
                            <td>
                                {{ number_format((float) $pesanan->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $pesanan->ppn, 2, ',', '.') }}
                            </td>
                            <td style="white-space:nowrap;">
                                {{ number_format((float) $pesanan->total_harga, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('pesanan-penjualan.update')
                                    <a
                                        href="{{ route('pesanan-penjualan.edit', $pesanan->no_pesanan) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                <a
                                    href="{{ route('pesanan-penjualan.print', $pesanan->no_pesanan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >Cetak</a>

                                @can('pesanan-penjualan.delete')
                                    <form
                                        action="{{ route('pesanan-penjualan.destroy', $pesanan->no_pesanan) }}"
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
                            <td colspan="7" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada pesanan penjualan
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Pesanan penjualan yang dibuat akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $pesanans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection