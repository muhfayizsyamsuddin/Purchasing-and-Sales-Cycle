@extends('gentelella::page')

@section('title', 'Pembayaran Penjualan')
@section('page_key', 'pembayaran-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pembayaran Penjualan')

@section('content')
    <x-gentelella::page-header
        title="Pembayaran Penjualan"
        pretitle="Sales Cycle"
    />

    <x-gentelella::card title="Daftar Pembayaran Penjualan">
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

        @can('pembayaran-penjualan.create')
            <div style="margin-bottom: 16px">
                <a
                    href="{{ route('pembayaran-penjualan.create') }}"
                    class="btn btn-primary"
                >
                    Tambah Pembayaran
                </a>
            </div>
        @endcan

        <div style="overflow-x: auto">
            <x-gentelella::table>
                <thead>
                    <tr>
                        <th>No. Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Terima Dari</th>
                        <th>No. Faktur</th>
                        <th>Untuk Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pembayarans as $pembayaran)
                        <tr>
                            <td>{{ $pembayaran->no_pembayaran }}</td>
                            <td>{{ $pembayaran->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $pembayaran->terima_dari }}</td>
                            <td>{{ $pembayaran->no_faktur }}</td>
                            <td>{{ $pembayaran->untuk_pembayaran }}</td>
                            <td>
                                Rp {{ number_format((float) $pembayaran->jumlah, 2, ',', '.') }}
                            </td>
                            <td>
                                @can('pembayaran-penjualan.view')
                                    <a
                                        href="{{ route('pembayaran-penjualan.print', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        Cetak
                                    </a>
                                @endcan

                                @can('pembayaran-penjualan.update')
                                    <a
                                        href="{{ route('pembayaran-penjualan.edit', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('pembayaran-penjualan.delete')
                                    <form
                                        action="{{ route('pembayaran-penjualan.destroy', $pembayaran->no_pembayaran) }}"
                                        method="POST"
                                        style="display: inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')"
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
                                Belum ada pembayaran penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </div>

        <div style="margin-top: 16px">
            {{ $pembayarans->links() }}
        </div>
    </x-gentelella::card>
@endsection