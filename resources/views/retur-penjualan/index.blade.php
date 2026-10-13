
@extends('gentelella::page')

@section('title', 'Retur Penjualan')
@section('page_key', 'retur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Retur Penjualan')

@section('content')
    <x-gentelella::page-header
        title="Retur Penjualan"
        pretitle="Sales Cycle"
    />

    <x-gentelella::card title="Daftar Retur Penjualan">
        @if (session('success'))
            <div class="banner banner-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="banner banner-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @can('retur-penjualan.create')
            <div style="margin-bottom: 16px">
                <a
                    href="{{ route('retur-penjualan.create') }}"
                    class="btn btn-primary"
                >
                    Tambah Retur
                </a>
            </div>
        @endcan

        <div style="overflow-x: auto">
            <x-gentelella::table>
                <thead>
                    <tr>
                        <th>No. Retur</th>
                        <th>Tanggal</th>
                        <th>ID Pengiriman</th>
                        <th>No. Pesanan</th>
                        <th>Nama Pembeli</th>
                        <th>Nama Penjual</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th>Jumlah Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($returs as $retur)
                        <tr>
                            <td>{{ $retur->no_retur }}</td>
                            <td>{{ $retur->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $retur->id_pengiriman_pesanan }}</td>
                            <td>
                                {{ $retur->pengirimanPesanan?->pesananPenjualan?->no_pesanan ?? '-' }}
                            </td>
                            <td>{{ $retur->nama_pembeli }}</td>
                            <td>{{ $retur->nama_penjual }}</td>
                            <td>
                                {{ number_format((float) $retur->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $retur->PPN, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $retur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>{{ $retur->details_count }}</td>
                            <td>
                                @can('retur-penjualan.view')
                                    <a
                                        href="{{ route('retur-penjualan.print', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                    >
                                        Cetak
                                    </a>
                                @endcan

                                @can('retur-penjualan.update')
                                    <a
                                        href="{{ route('retur-penjualan.edit', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('retur-penjualan.delete')
                                    <form
                                        action="{{ route('retur-penjualan.destroy', $retur->no_retur) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus retur penjualan ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                Belum ada retur penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $returs->links() }}
        </div>
    </x-gentelella::card>
@endsection
