
@extends('gentelella::page')

@section('title', 'Master Vendor')
@section('page_key', 'vendor')
@section('breadcrumb', 'Home > Master Data > Vendor')

@section('content')
    <x-gentelella::page-header
        title="Master Vendor"
        pretitle="Master Data"
    >
        <x-slot:actions>
            @can('vendor.create')
                <x-gentelella::btn
                    :href="route('vendor.create')"
                    variant="primary"
                >
                    Tambah Vendor
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Vendor">
        @if (session('success'))
            <div class="banner banner-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>ID Vendor</th>
                    <th>Nama Vendor</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->id_vendor }}</td>
                        <td>{{ $vendor->nama_vendor }}</td>
                        <td>
                            @can('vendor.update')
                                <a
                                    href="{{ route('vendor.edit', $vendor->id_vendor) }}"
                                    class="btn btn-sm btn-outline"
                                >
                                    Edit
                                </a>
                            @endcan

                            @can('vendor.delete')
                                <form
                                    action="{{ route('vendor.destroy', $vendor->id_vendor) }}"
                                    method="POST"
                                    style="display: inline"
                                    onsubmit="return confirm('Yakin ingin menghapus vendor ini?')"
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
                        <td colspan="3">Belum ada data vendor.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        <div style="margin-top: 16px">
            {{ $vendors->links() }}
        </div>
    </x-gentelella::card>
@endsection
