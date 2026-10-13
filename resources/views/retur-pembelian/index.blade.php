
@extends('gentelella::page')

@section('title', 'Retur Pembelian')
@section('page_key', 'retur-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Retur Pembelian')

@section('content')
    <x-gentelella::page-header
        title="Retur Pembelian"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Daftar Retur Pembelian">
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

        @can('retur-pembelian.create')
            <div style="margin-bottom: 16px">
                <a
                    href="{{ route('retur-pembelian.create') }}"
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
                        <th>No. Penerimaan</th>
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
                            <td>{{ $retur->details_count }}</td>
                            <td>
                                @can('retur-pembelian.view')
                                    <a
                                        href="{{ route('retur-pembelian.cetak', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                    >
                                        Cetak
                                    </a>
                                @endcan
                                @can('retur-pembelian.update')
                                    <a
                                        href="{{ route('retur-pembelian.edit', $retur->no_retur) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('retur-pembelian.delete')
                                    <form
                                        action="{{ route('retur-pembelian.destroy', $retur->no_retur) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus retur pembelian ini?')"
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
                            <td colspan="10">
                                Belum ada retur pembelian.
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
