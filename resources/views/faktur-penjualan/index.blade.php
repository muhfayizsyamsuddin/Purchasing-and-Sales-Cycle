@extends('gentelella::page')

@section('title', 'Faktur Penjualan')
@section('page_key', 'faktur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Faktur Penjualan')

@section('content')
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

        <div style="overflow-x: auto">
            <x-gentelella::table>
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>No. Pengiriman</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($fakturs as $faktur)
                        <tr>
                            <td>{{ $faktur->no_faktur }}</td>
                            <td>{{ $faktur->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $faktur->nama }}</td>
                            <td>{{ $faktur->id_pengiriman_pesanan }}</td>
                            <td>
                                {{ number_format((float) $faktur->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->PPN, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('faktur-penjualan.update')
                                    <a
                                        href="{{ route('faktur-penjualan.edit', $faktur->no_faktur) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                <a
                                    href="{{ route('faktur-penjualan.print', $faktur->no_faktur) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >
                                    Cetak
                                </a>

                                @can('faktur-penjualan.delete')
                                    <form
                                        action="{{ route('faktur-penjualan.destroy', $faktur->no_faktur) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus faktur ini?')"
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
                            <td colspan="8">
                                Belum ada faktur penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $fakturs->links() }}
        </div>
    </x-gentelella::card>
@endsection