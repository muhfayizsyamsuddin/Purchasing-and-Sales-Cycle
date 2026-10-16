
@extends('gentelella::page')

@section('title', 'Pembayaran Pembelian')
@section('page_key', 'pembayaran-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pembayaran Pembelian')

@section('content')
    <div class="pembayaran-pembelian-page">
        <x-gentelella::page-header
            title="Pembayaran Pembelian"
            pretitle="Purchasing Cycle"
        >
            <x-slot:actions>
                @can('pembayaran-pembelian.create')
                    <x-gentelella::btn
                        :href="route('pembayaran-pembelian.create')"
                        variant="primary"
                    >
                        Tambah Pembayaran
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Pembayaran Pembelian">
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
                .pembayaran-pembelian-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .pembayaran-pembelian-table th,
                .pembayaran-pembelian-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .pembayaran-pembelian-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="pembayaran-pembelian-table"
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
                                @can('pembayaran-pembelian.view')
                                    <a
                                        href="{{ route('pembayaran-pembelian.cetak', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                        rel="noopener"
                                    >Cetak</a>
                                @endcan

                                @can('pembayaran-pembelian.update')
                                    <a
                                        href="{{ route('pembayaran-pembelian.edit', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                @can('pembayaran-pembelian.delete')
                                    <form
                                        action="{{ route('pembayaran-pembelian.destroy', $pembayaran->no_pembayaran) }}"
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
                                    Belum ada pembayaran pembelian
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data pembayaran yang dibuat akan muncul di sini.
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