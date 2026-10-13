
@extends('gentelella::page')

@section('title', 'Pembayaran Pembelian')
@section('page_key', 'pembayaran-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pembayaran Pembelian')

@section('content')
    <x-gentelella::page-header
        title="Pembayaran Pembelian"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Daftar Pembayaran Pembelian">
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

        @can('pembayaran-pembelian.create')
            <div style="margin-bottom: 16px">
                <a
                    href="{{ route('pembayaran-pembelian.create') }}"
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
                                @can('pembayaran-pembelian.view')
                                    <a
                                        href="{{ route('pembayaran-pembelian.cetak', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                        target="_blank"
                                    >
                                        Cetak
                                    </a>
                                @endcan
                                @can('pembayaran-pembelian.update')
                                    <a
                                        href="{{ route('pembayaran-pembelian.edit', $pembayaran->no_pembayaran) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('pembayaran-pembelian.delete')
                                    <form
                                        action="{{ route('pembayaran-pembelian.destroy', $pembayaran->no_pembayaran) }}"
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
                                Belum ada pembayaran pembelian.
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
