@extends('gentelella::page')

@section('title', 'Permintaan Pembelian')
@section('page_key', 'permintaan-pembelian')
@section('breadcrumb', 'Home > Transaksi > Permintaan Pembelian')

@section('content')
    <x-gentelella::page-header
        title="Permintaan Pembelian"
        pretitle="Purchasing Cycle"
    >
        <x-slot:actions>
            @can('permintaan-pembelian.create')
                <x-gentelella::btn
                    :href="route('permintaan-pembelian.create')"
                    variant="primary"
                >
                    Buat Permintaan
                </x-gentelella::btn>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar Permintaan Pembelian">
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

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Tanggal Diperlukan</th>
                    <th>Status</th>
                    <th>Jumlah Detail</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($permintaans as $permintaan)
                    <tr>
                        <td>{{ $permintaan->no_faktur }}</td>
                        <td>{{ $permintaan->tanggal }}</td>
                        <td>{{ $permintaan->tanggal_diperlukan }}</td>
                        <td>{{ $permintaan->status }}</td>
                        <td>{{ $permintaan->details->count() }}</td>
                        <td>
                            @can('permintaan-pembelian.update')
                                <a
                                    href="{{ route('permintaan-pembelian.edit', $permintaan->no_faktur) }}"
                                    class="btn btn-sm btn-outline"
                                >
                                    Edit
                                </a>
                            @endcan
                            @can('permintaan-pembelian.print')
                                <a
                                    href="{{ route('permintaan-pembelian.print', $permintaan->no_faktur) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn btn-sm btn-outline"
                                >
                                    Cetak
                                </a>
                            @endcan

                            @can('permintaan-pembelian.delete')
                                <form
                                    action="{{ route('permintaan-pembelian.destroy', $permintaan->no_faktur) }}"
                                    method="POST"
                                    style="display: inline"
                                    onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')"
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
                            Belum ada permintaan pembelian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        <div style="margin-top: 16px">
            {{ $permintaans->links() }}
        </div>
    </x-gentelella::card>
@endsection