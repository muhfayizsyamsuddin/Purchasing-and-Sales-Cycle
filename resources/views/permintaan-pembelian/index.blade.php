@extends('gentelella::page')

@section('title', 'Permintaan Pembelian')
@section('page_key', 'permintaan-pembelian')
@section('breadcrumb', 'Home > Transaksi > Permintaan Pembelian')

@section('content')
    <div class="permintaan-pembelian-page">
        <x-gentelella::page-header
            title="Permintaan Pembelian"
            pretitle="Purchasing Cycle"
        >
            <x-slot:actions>
                @can('permintaan-pembelian.create')
                    <x-gentelella::btn
                        :href="route('permintaan-pembelian.create')"
                        variant="primary"
                    >
                        Buat Permintaan
                    </x-gentelella::btn>
                @endcan
            </x-slot:actions>
        </x-gentelella::page-header>

        <x-gentelella::card title="Daftar Permintaan Pembelian">
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
                .permintaan-pembelian-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .permintaan-pembelian-table th,
                .permintaan-pembelian-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .permintaan-pembelian-table td:last-child {
                    white-space: nowrap;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="permintaan-pembelian-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:20%">
                    <col style="width:16%">
                    <col style="width:20%">
                    <col style="width:14%">
                    <col style="width:30%">
                </colgroup>

                <thead>
                    <tr>
                        <th>No. Permintaan</th>
                        <th>Tanggal</th>
                        <th>Tanggal Diperlukan</th>
                        <th>Total Kuantitas</th>
                        <th data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($permintaans as $permintaan)
                        <tr>
                            <td>{{ $permintaan->no_faktur }}</td>
                            <td>
                                {{ $permintaan->tanggal ? \Carbon\Carbon::parse($permintaan->tanggal)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                {{ $permintaan->tanggal_diperlukan ? \Carbon\Carbon::parse($permintaan->tanggal_diperlukan)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $permintaan->details_sum_kuantitas ?? 0 }}</td>
                            <td>
                                @can('permintaan-pembelian.update')
                                    <a
                                        href="{{ route('permintaan-pembelian.edit', $permintaan->no_faktur) }}"
                                        class="btn btn-sm btn-outline"
                                    >Edit</a>
                                @endcan

                                @can('permintaan-pembelian.print')
                                    <a
                                        href="{{ route('permintaan-pembelian.print', $permintaan->no_faktur) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-sm btn-outline"
                                    >Cetak</a>
                                @endcan

                                @can('permintaan-pembelian.delete')
                                    <form
                                        action="{{ route('permintaan-pembelian.destroy', $permintaan->no_faktur) }}"
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
                            <td colspan="5" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada permintaan pembelian
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Permintaan pembelian yang dibuat akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $permintaans->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection