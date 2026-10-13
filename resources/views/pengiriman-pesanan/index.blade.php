@extends('gentelella::page')

@section('title', 'Pengiriman Pesanan')
@section('page_key', 'pengiriman-pesanan')
@section('breadcrumb', 'Home > Sales Cycle > Pengiriman Pesanan')

@section('content')
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

        <div style="overflow-x: auto">
            <x-gentelella::table>
                <thead>
                    <tr>
                        <th>ID Pengiriman</th>
                        <th>Tanggal</th>
                        <th>No. Pesanan</th>
                        <th>Nama Pembeli</th>
                        <th>Nama Pengirim</th>
                        <th>Jumlah Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengirimans as $pengiriman)
                        <tr>
                            <td>{{ $pengiriman->id_pengiriman_pesanan }}</td>
                            <td>
                                {{ $pengiriman->tanggal->format('d/m/Y') }}
                            </td>
                            <td>{{ $pengiriman->no_pesanan }}</td>
                            <td>
                                {{ $pengiriman->pesananPenjualan?->nama_pembeli ?? '-' }}
                            </td>
                            <td>{{ $pengiriman->nama_pengirim }}</td>
                            <td>{{ $pengiriman->details->count() }}</td>
                            <td>
                                @can('pengiriman-pesanan.update')
                                    <a
                                        href="{{ route('pengiriman-pesanan.edit', $pengiriman->id_pengiriman_pesanan) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan
                                <a
                                    href="{{ route('pengiriman-pesanan.print', $pengiriman->id_pengiriman_pesanan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >
                                    Cetak
                                </a>

                                @can('pengiriman-pesanan.delete')
                                    <form
                                        action="{{ route('pengiriman-pesanan.destroy', $pengiriman->id_pengiriman_pesanan) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pengiriman ini?')"
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
                                Belum ada pengiriman pesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $pengirimans->links() }}
        </div>
    </x-gentelella::card>
@endsection