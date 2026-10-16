@extends('gentelella::page')

@section('title', 'Pesanan Pembelian')
@section('page_key', 'pesanan-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pesanan Pembelian')

@section('content')
    <div class="pesanan-pembelian-page">
        <x-gentelella::page-header
            title="Pesanan Pembelian"
            pretitle="Purchasing Cycle"
        >
            <x-slot:actions>
                @can('pesanan-pembelian.create')
                    <x-gentelella::btn
                        :href="route('pesanan-pembelian.create')"
                        variant="primary"
                    >
                        Buat Pesanan
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Pesanan Pembelian">
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
                .pesanan-pembelian-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .pesanan-pembelian-table th:nth-child(-n+7),
                .pesanan-pembelian-table td:nth-child(-n+7) {
                    width: 12% !important;
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pesanan-pembelian-table th:nth-child(8),
                .pesanan-pembelian-table td:nth-child(8) {
                    width: 16% !important;
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pesanan-pembelian-table td {
                    overflow-wrap: anywhere;
                }

                .pesanan-pembelian-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="pesanan-pembelian-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:16%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Tanggal</th>
                        <th>Tanggal Dibutuhkan</th>
                        <th>Vendor</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->no_order_pembelian }}</td>
                            <td>{{ $pesanan->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pesanan->tanggal_dibutuhkan?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pesanan->vendor?->nama_vendor ?? '-' }}</td>
                            <td>
                                {{ number_format((float) $pesanan->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $pesanan->ppn, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $pesanan->total_harga, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('pesanan-pembelian.update')
                                    <a
                                        href="{{ route('pesanan-pembelian.edit', $pesanan->no_order_pembelian) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                <a
                                    href="{{ route('pesanan-pembelian.print', $pesanan->no_order_pembelian) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >Cetak</a>

                                @can('pesanan-pembelian.delete')
                                    <form
                                        action="{{ route('pesanan-pembelian.destroy', $pesanan->no_order_pembelian) }}"
                                        method="POST"
                                        style="display:inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline">
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:32px 16px;">
                                Belum ada pesanan pembelian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px">
                {{ $pesanans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection