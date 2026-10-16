@extends('gentelella::page')

@section('title', 'Pembayaran Penjualan')
@section('page_key', 'pembayaran-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pembayaran Penjualan')

@section('content')
    <div class="pembayaran-penjualan-page">
        <x-gentelella::page-header
            title="Pembayaran Penjualan"
            pretitle="Sales Cycle"
        >
            <x-slot:actions>
                @can('pembayaran-penjualan.create')
                    <x-gentelella::btn
                        :href="route('pembayaran-penjualan.create')"
                        variant="primary"
                    >
                        Tambah Pembayaran
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Pembayaran Penjualan">
            <!-- @if (session('success'))
                <div class="banner banner-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="banner banner-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif -->

            <style>
                .pembayaran-penjualan-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .pembayaran-penjualan-table th,
                .pembayaran-penjualan-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pembayaran-penjualan-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="pembayaran-penjualan-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:14%">
                    <col style="width:10%">
                    <col style="width:14%">
                    <col style="width:12%">
                    <col style="width:16%">
                    <col style="width:14%">
                    <col style="width:20%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Terima Dari</th>
                        <th>No. Faktur</th>
                        <th>Untuk Pembayaran</th>
                        <th>Jumlah</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pembayarans as $pembayaran)
                        <tr>
                            <td>{{ $pembayaran->no_pembayaran }}</td>
                            <td>
                                {{ $pembayaran->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>{{ $pembayaran->terima_dari }}</td>
                            <td>{{ $pembayaran->no_faktur }}</td>
                            <td>{{ $pembayaran->untuk_pembayaran }}</td>
                            <td style="white-space:nowrap;">
                                Rp {{ number_format((float) $pembayaran->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('pembayaran-penjualan.view')
                                    <a
                                        href="{{ route('pembayaran-penjualan.print', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                        rel="noopener"
                                    >Cetak</a>
                                @endcan

                                @can('pembayaran-penjualan.update')
                                    <a
                                        href="{{ route('pembayaran-penjualan.edit', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                @can('pembayaran-penjualan.delete')
                                    <form
                                        action="{{ route('pembayaran-penjualan.destroy', $pembayaran->no_pembayaran) }}"
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
                                    Belum ada pembayaran penjualan
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data pembayaran penjualan yang dibuat akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $pembayarans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection