
@extends('gentelella::page')

@section('title', 'Master Barang')
@section('page_key', 'barang')
@section('breadcrumb', 'Home > Master Data > Barang')

@section('content')
    <div class="barang-page">
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

            <x-gentelella::table :datatable="true" :page-length="10" style="width:100%; table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="width:20%;">SKU</th>
                        <th style="width:55%;">Nama Barang</th>
                        <th style="width:25%; text-align:center;" data-orderable="false">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($barangs as $barang)
                        <tr>
                            <td>{{ $barang->sku }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td style="text-align: center; white-space: nowrap;">
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

                                        <button type="submit" class="btn btn-sm btn-outline">
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 32px 16px;">
                                <div style="font-weight: 600; margin-bottom: 4px;">
                                    Belum ada data barang
                                </div>
                                <div style="color: var(--text-muted); font-size: 13px;">
                                    Data barang yang ditambahkan akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top: 16px">
                {{ $barangs->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection
