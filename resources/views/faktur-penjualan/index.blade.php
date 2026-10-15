
@extends('gentelella::page')

@section('title', 'Faktur Penjualan')
@section('page_key', 'faktur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Faktur Penjualan')

@section('content')
    <div class="faktur-penjualan-page">
        <x-gentelella::page-header
            title="Faktur Penjualan"
            pretitle="Sales Cycle"
        >
            <x-slot:actions>
                @can('faktur-penjualan.create')
                    <x-gentelella::btn
                        :href="route('faktur-penjualan.create')"
                        variant="primary"
                    >
                        Buat Faktur
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Faktur Penjualan">
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
                .faktur-penjualan-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .faktur-penjualan-table th,
                .faktur-penjualan-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .faktur-penjualan-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="faktur-penjualan-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:13%">
                    <col style="width:10%">
                    <col style="width:15%">
                    <col style="width:14%">
                    <col style="width:10%">
                    <col style="width:8%">
                    <col style="width:14%">
                    <col style="width:16%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Tanggal Pengiriman</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($fakturs as $faktur)
                        <tr>
                            <td>{{ $faktur->no_faktur }}</td>
                            <td>{{ $faktur->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $faktur->nama }}</td>
                            <td>
                                {{ $faktur->pengirimanPesanan?->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->ppn, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('faktur-penjualan.update')
                                    <a
                                        href="{{ route('faktur-penjualan.edit', $faktur->no_faktur) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                <a
                                    href="{{ route('faktur-penjualan.print', $faktur->no_faktur) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >Cetak</a>

                                @can('faktur-penjualan.delete')
                                    <form
                                        action="{{ route('faktur-penjualan.destroy', $faktur->no_faktur) }}"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Yakin ingin menghapus faktur ini?')"
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
                            <td colspan="8" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada faktur penjualan
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Faktur penjualan yang ditambahkan akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $fakturs->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection