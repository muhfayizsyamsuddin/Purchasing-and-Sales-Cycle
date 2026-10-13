@extends('gentelella::page')

@section('title', 'Pesanan Penjualan')
@section('page_key', 'pesanan-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pesanan Penjualan')

@section('content')
    <x-gentelella::page-header
        title="Pesanan Penjualan"
        pretitle="Sales Cycle"
    >
        <x-slot:actions>
            @can('pesanan-penjualan.create')
                <x-gentelella::btn
                    :href="route('pesanan-penjualan.create')"
                    variant="primary"
                >
                    Buat Pesanan
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Pesanan Penjualan">
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

        <div style="overflow-x: auto">
            <x-gentelella::table>
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->no_pesanan }}</td>
                            <td>{{ $pesanan->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $pesanan->nama_pembeli }}</td>
                            <td>
                                {{ number_format((float) $pesanan->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $pesanan->PPN, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $pesanan->total_harga, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('pesanan-penjualan.update')
                                    <a
                                        href="{{ route('pesanan-penjualan.edit', $pesanan->no_pesanan) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan
                                <a
                                    href="{{ route('pesanan-penjualan.print', $pesanan->no_pesanan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >
                                    Cetak
                                </a>

                                @can('pesanan-penjualan.delete')
                                    <form
                                        action="{{ route('pesanan-penjualan.destroy', $pesanan->no_pesanan) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')"
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
                            <td colspan="7">
                                Belum ada pesanan penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $pesanans->links() }}
        </div>
    </x-gentelella::card>
@endsection