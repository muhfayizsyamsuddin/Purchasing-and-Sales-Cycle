
@extends('gentelella::page')

@section('title', 'Pengiriman Pesanan')
@section('page_key', 'pengiriman-pesanan')
@section('breadcrumb', 'Home > Sales Cycle > Pengiriman Pesanan')

@section('content')
    <div class="pengiriman-pesanan-page">
        <x-gentelella::page-header
            title="Pengiriman Pesanan"
            pretitle="Sales Cycle"
        >
            <x-slot:actions>
                @can('pengiriman-pesanan.create')
                    <x-gentelella::btn
                        :href="route('pengiriman-pesanan.create')"
                        variant="primary"
                    >
                        Tambah Pengiriman
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Pengiriman Pesanan">
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
                .pengiriman-pesanan-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .pengiriman-pesanan-table th,
                .pengiriman-pesanan-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pengiriman-pesanan-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="pengiriman-pesanan-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:13%">
                    <col style="width:10%">
                    <col style="width:14%">
                    <col style="width:16%">
                    <col style="width:14%">
                    <col style="width:11%">
                    <col style="width:22%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>No. Pesanan</th>
                        <th>Nama Pembeli</th>
                        <th>Nama Pengirim</th>
                        <th>Total Kuantitas</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengirimans as $pengiriman)
                        <tr>
                            <td>{{ $pengirimans->firstItem() + $loop->index }}</td>
                            <td>
                                {{ $pengiriman->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>{{ $pengiriman->no_pesanan }}</td>
                            <td>
                                {{ $pengiriman->pesananPenjualan?->nama_pembeli ?? '-' }}
                            </td>
                            <td>{{ $pengiriman->nama_pengirim }}</td>
                            <td>{{ $pengiriman->details_sum_total ?? 0 }}</td>
                            <td>
                                @can('pengiriman-pesanan.update')
                                    <a
                                        href="{{ route('pengiriman-pesanan.edit', $pengiriman->id_pengiriman_pesanan) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                <a
                                    href="{{ route('pengiriman-pesanan.print', $pengiriman->id_pengiriman_pesanan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >Cetak</a>

                                @can('pengiriman-pesanan.delete')
                                    <form
                                        action="{{ route('pengiriman-pesanan.destroy', $pengiriman->id_pengiriman_pesanan) }}"
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
                                    Belum ada pengiriman pesanan
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data pengiriman yang dibuat akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $pengirimans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection