@extends('gentelella::page')

@section('title', 'Master Vendor')
@section('page_key', 'vendor')
@section('breadcrumb', 'Home > Master Data > Vendor')

@section('content')
    <div class="barang-page">
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

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                style="width:100%; table-layout:fixed;"
            >
                <thead>
                    <tr>
                        <th style="width:10%;">No.</th>
                        <th style="width:65%;">Nama Vendor</th>
                        <th
                            style="width:25%; text-align:center;"
                            data-orderable="false"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($vendors as $vendor)
                        <tr>
                            <td>{{ $vendors->firstItem() + $loop->index }}</td>
                            <td>{{ $vendor->nama_vendor }}</td>
                            <td style="text-align:center; white-space:nowrap;">
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
                                        style="display:inline"
                                        onsubmit="return confirm('Yakin ingin menghapus vendor ini?')"
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
                            <td colspan="3" style="text-align:center; padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada data vendor
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data vendor yang ditambahkan akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px">
                {{ $vendors->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection