
@extends('gentelella::page')

@section('title', 'Retur Pembelian')
@section('page_key', 'retur-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Retur Pembelian')

@section('content')
    <div class="retur-pembelian-page">
        <x-gentelella::page-header
            title="Retur Pembelian"
            pretitle="Purchasing Cycle"
        >
            <x-slot:actions>
                @can('retur-pembelian.create')
                    <x-gentelella::btn
                        :href="route('retur-pembelian.create')"
                        variant="primary"
                    >
                        Tambah Retur
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Retur Pembelian">
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
                .retur-pembelian-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .retur-pembelian-table th,
                .retur-pembelian-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .retur-pembelian-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="retur-pembelian-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:10%">
                    <col style="width:8%">
                    <col style="width:10%">
                    <col style="width:10%">
                    <col style="width:10%">
                    <col style="width:7%">
                    <col style="width:7%">
                    <col style="width:10%">
                    <col style="width:11%">
                    <col style="width:17%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Retur</th>
                        <th>Tanggal</th>
                        <th>No. Penerimaan</th>
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
                            <td>{{ $retur->no_penerimaan }}</td>
                            <td>{{ $retur->nama_pembeli }}</td>
                            <td>{{ $retur->nama_penjual }}</td>
                            <td>
                                {{ number_format((float) $retur->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $retur->ppn, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $retur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>{{ $retur->details_sum_jumlah ?? 0 }}</td>
                            <td>
                                @can('retur-pembelian.view')
                                    <a
                                        href="{{ route('retur-pembelian.cetak', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                        rel="noopener"
                                    >Cetak</a>
                                @endcan

                                @can('retur-pembelian.update')
                                    <a
                                        href="{{ route('retur-pembelian.edit', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                @can('retur-pembelian.delete')
                                    <form
                                        action="{{ route('retur-pembelian.destroy', $retur->no_retur) }}"
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
                            <td colspan="10" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada retur pembelian
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data retur pembelian yang dibuat akan muncul di sini.
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