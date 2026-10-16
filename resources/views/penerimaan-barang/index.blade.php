@extends('gentelella::page')

@section('title', 'Penerimaan Barang')
@section('page_key', 'penerimaan-barang')
@section('breadcrumb', 'Home > Purchasing Cycle > Penerimaan Barang')

@section('content')
    <div class="penerimaan-barang-page">
        <x-gentelella::page-header
            title="Penerimaan Barang"
            pretitle="Purchasing Cycle"
        >
            <x-slot:actions>
                @can('penerimaan-barang.create')
                    <x-gentelella::btn
                        :href="route('penerimaan-barang.create')"
                        variant="primary"
                    >
                        Tambah Penerimaan
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Penerimaan Barang">
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
                .penerimaan-barang-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .penerimaan-barang-table th,
                .penerimaan-barang-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .penerimaan-barang-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="penerimaan-barang-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:18%">
                    <col style="width:16%">
                    <col style="width:20%">
                    <col style="width:18%">
                    <col style="width:12%">
                    <col style="width:16%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Penerimaan</th>
                        <th>Tanggal Penerimaan</th>
                        <th>No. Order Pembelian</th>
                        <th>Vendor</th>
                        <th>Jumlah Detail</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($penerimaans as $penerimaan)
                        <tr>
                            <td>{{ $penerimaan->no_penerimaan }}</td>
                            <td>
                                {{ $penerimaan->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>{{ $penerimaan->no_order_pembelian }}</td>
                            <td>{{ $penerimaan->pesanan?->vendor?->nama_vendor ?? '-' }}</td>
                            <td>{{ $penerimaan->details_count }}</td>
                            <td>
                                @can('penerimaan-barang.update')
                                    <a
                                        href="{{ route('penerimaan-barang.edit', $penerimaan->no_penerimaan) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                <a
                                    href="{{ route('penerimaan-barang.print', $penerimaan->no_penerimaan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >Cetak</a>

                                @can('penerimaan-barang.delete')
                                    <form
                                        action="{{ route('penerimaan-barang.destroy', $penerimaan->no_penerimaan) }}"
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
                            <td colspan="6" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada penerimaan barang
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data penerimaan barang akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $penerimaans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection