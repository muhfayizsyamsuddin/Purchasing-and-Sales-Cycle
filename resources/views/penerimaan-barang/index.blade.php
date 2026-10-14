
@extends('gentelella::page')

@section('title', 'Penerimaan Barang')
@section('page_key', 'penerimaan-barang')
@section('breadcrumb', 'Home > Purchasing Cycle > Penerimaan Barang')

@section('content')
    <x-gentelella::page-header
        title="Penerimaan Barang"
        pretitle="Purchasing Cycle"
    >
        <x-slot:actions>
            @can('penerimaan-barang.create')
                <x-gentelella::btn
                    :href="route('penerimaan-barang.create')"
                    variant="primary"
                >
                    Tambah Penerimaan
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Penerimaan Barang">
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
                        <th>No. Penerimaan</th>
                        <th>Tanggal Penerimaan</th>
                        <th>No. Order Pembelian</th>
                        <th>Vendor</th>
                        <th>Jumlah Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($penerimaans as $penerimaan)
                        <tr>
                            <td>{{ $penerimaan->no_penerimaan }}</td>
                            <td>
                                {{ $penerimaan->tanggal_penerimaan?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                {{ $penerimaan->no_order_pembelian }}
                            </td>
                            <td>
                                {{ $penerimaan->pesanan?->vendor?->nama_vendor ?? '-' }}
                            </td>
                            <td>{{ $penerimaan->details_count }}</td>
                            <td>
                                @can('penerimaan-barang.update')
                                    <a
                                        href="{{ route('penerimaan-barang.edit', $penerimaan->no_penerimaan) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan
                                <a
                                    href="{{ route('penerimaan-barang.print', $penerimaan->no_penerimaan) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >
                                    Cetak
                                </a>

                                @can('penerimaan-barang.delete')
                                    <form
                                        action="{{ route('penerimaan-barang.destroy', $penerimaan->no_penerimaan) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus penerimaan ini?')"
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
                            <td colspan="6">
                                Belum ada penerimaan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $penerimaans->links() }}
        </div>
    </x-gentelella::card>
@endsection
