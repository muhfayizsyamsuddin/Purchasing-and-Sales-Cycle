
@extends('gentelella::page')

@section('title', 'Faktur Pembelian')
@section('page_key', 'faktur-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Faktur Pembelian')

@section('content')
    <x-gentelella::page-header
        title="Faktur Pembelian"
        pretitle="Purchasing Cycle"
    >
        <x-slot:actions>
            @can('faktur-pembelian.create')
                <x-gentelella::btn
                    :href="route('faktur-pembelian.create')"
                    variant="primary"
                >
                    Tambah Faktur
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Faktur Pembelian">
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
                        <th>Nama</th>
                        <th>No. Penerimaan</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>Total Harga</th>
                        <th>Jumlah Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($fakturs as $faktur)
                        <tr>
                            <td>{{ $faktur->no_faktur }}</td>
                            <td>{{ $faktur->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $faktur->nama }}</td>
                            <td>{{ $faktur->no_penerimaan }}</td>
                            <td>
                                {{ number_format((float) $faktur->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->ppn, 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format((float) $faktur->total_harga, 2, ',', '.') }}
                            </td>
                            <td>{{ $faktur->details_count }}</td>
                            <td>
                                @can('faktur-pembelian.view')
                                    <a
                                        href="{{ route('faktur-pembelian.cetak', $faktur->no_faktur) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                    >
                                        Cetak
                                    </a>
                                @endcan
                                @can('faktur-pembelian.update')
                                    <a
                                        href="{{ route('faktur-pembelian.edit', $faktur->no_faktur) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('faktur-pembelian.delete')
                                    <form
                                        action="{{ route('faktur-pembelian.destroy', $faktur->no_faktur) }}"
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
                            <td colspan="9">
                                Belum ada faktur pembelian.
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
