
@extends('gentelella::page')

@section('title', 'Master Barang')
@section('page_key', 'barang')
@section('breadcrumb', 'Home > Master Data > Barang')

@section('content')
    <x-gentelella::page-header
        title="Master Barang"
        pretitle="Master Data"
    >
        <x-slot:actions>
            @can('barang.create')
                <x-gentelella::btn
                    :href="route('barang.create')"
                    variant="primary"
                >
                    Tambah Barang
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Barang">
        @if (session('success'))
            <div class="banner banner-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Barang</th>
                    <th data-orderable="false">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($barangs as $barang)
                    <tr>
                        <td>{{ $barang->sku }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>
                            @can('barang.update')
                                <a
                                    href="{{ route('barang.edit', $barang->sku) }}"
                                    class="btn btn-sm btn-outline"
                                >
                                    Edit
                                </a>
                            @endcan

                            @can('barang.delete')
                                <form
                                    action="{{ route('barang.destroy', $barang->sku) }}"
                                    method="POST"
                                    style="display: inline"
                                    onsubmit="return confirm('Yakin ingin menghapus barang ini?')"
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
                        <td colspan="3">
                            Belum ada data barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        <div style="margin-top: 16px">
            {{ $barangs->links() }}
        </div>
    </x-gentelella::card>
@endsection
