@extends('gentelella::page')

@section('title', 'Edit Pesanan Pembelian')
@section('page_key', 'pesanan-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pesanan Pembelian > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit Pesanan Pembelian"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Informasi Pesanan">
        @if ($errors->any())
            <div class="banner banner-danger" role="alert">
                <strong>Periksa kembali input berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="banner banner-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @php
            $details = old(
                'details',
                $pesanan->details->map(fn ($detail) => [
                    'sku' => $detail->sku,
                    'kuantitas' => $detail->kuantitas,
                    'harga_unit' => $detail->harga_unit,
                    'diskon' => $detail->diskon,
                ])->all()
            );
        @endphp

        <form
            action="{{ route('pesanan-pembelian.update', $pesanan->no_order_pembelian) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_order_pembelian" class="form-label">
                    No. Order Pembelian
                </label>
                <input
                    type="text"
                    id="no_order_pembelian"
                    name="no_order_pembelian"
                    value="{{ $pesanan->no_order_pembelian }}"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="{{ old('tanggal', $pesanan->tanggal->format('Y-m-d')) }}"
                    class="form-control"
                    required
                >
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_dibutuhkan" class="form-label">
                    Tanggal Dibutuhkan
                </label>
                <input
                    type="date"
                    id="tanggal_dibutuhkan"
                    name="tanggal_dibutuhkan"
                    value="{{ old('tanggal_dibutuhkan', $pesanan->tanggal_dibutuhkan->format('Y-m-d')) }}"
                    class="form-control"
                    required
                >
                @error('tanggal_dibutuhkan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="id_vendor" class="form-label">Vendor</label>
                <select
                    id="id_vendor"
                    name="id_vendor"
                    class="form-control"
                    required
                >
                    <option value="">Pilih vendor</option>
                    @foreach ($vendors as $vendor)
                        <option
                            value="{{ $vendor->id_vendor }}"
                            @selected(old('id_vendor', $pesanan->id_vendor) == $vendor->id_vendor)
                        >
                            {{ $vendor->nama_vendor }}
                        </option>
                    @endforeach
                </select>
                @error('id_vendor')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="no_faktur" class="form-label">No. Faktur</label>
                <input
                    type="text"
                    id="no_faktur"
                    name="no_faktur"
                    value="{{ old('no_faktur', $pesanan->no_faktur) }}"
                    class="form-control"
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="ppn" class="form-label">PPN (nominal)</label>
                <input
                    type="number"
                    id="ppn"
                    name="ppn"
                    value="{{ old('ppn', $pesanan->ppn) }}"
                    class="form-control"
                    min="0"
                    step="0.01"
                    required
                >
                @error('ppn')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <h3>Detail Barang</h3>

            <div style="overflow-x: auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang / SKU</th>
                            <th>Kuantitas</th>
                            <th>Harga Unit</th>
                            <th>Diskon (nominal)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="detail-rows">
                        @foreach ($details as $index => $detail)
                            <tr data-detail-row>
                                <td>
                                    <select
                                        name="details[{{ $index }}][sku]"
                                        class="form-control"
                                        required
                                    >
                                        <option value="">Pilih barang</option>
                                        @foreach ($barangs as $barang)
                                            <option
                                                value="{{ $barang->sku }}"
                                                @selected(($detail['sku'] ?? '') == $barang->sku)
                                            >
                                                {{ $barang->sku }} — {{ $barang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="details[{{ $index }}][kuantitas]"
                                        class="form-control"
                                        min="1"
                                        value="{{ $detail['kuantitas'] ?? 1 }}"
                                        required
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="details[{{ $index }}][harga_unit]"
                                        class="form-control"
                                        min="0"
                                        step="0.01"
                                        value="{{ $detail['harga_unit'] ?? 0 }}"
                                        required
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="details[{{ $index }}][diskon]"
                                        class="form-control"
                                        min="0"
                                        step="0.01"
                                        value="{{ $detail['diskon'] ?? 0 }}"
                                    >
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline"
                                        data-remove-row
                                    >
                                        Hapus Baris
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button
                type="button"
                id="add-detail"
                class="btn btn-outline"
            >
                Tambah Baris Barang
            </button>

            <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('pesanan-pembelian.index') }}"
                    class="btn btn-outline"
                >
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>

    <template id="detail-row-template">
        <tr data-detail-row>
            <td>
                <select
                    name="details[__INDEX__][sku]"
                    class="form-control"
                    required
                >
                    <option value="">Pilih barang</option>
                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->sku }}">
                            {{ $barang->sku }} — {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>
                <input
                    type="number"
                    name="details[__INDEX__][kuantitas]"
                    class="form-control"
                    min="1"
                    value="1"
                    required
                >
            </td>

            <td>
                <input
                    type="number"
                    name="details[__INDEX__][harga_unit]"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="0"
                    required
                >
            </td>

            <td>
                <input
                    type="number"
                    name="details[__INDEX__][diskon]"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="0"
                >
            </td>

            <td>
                <button
                    type="button"
                    class="btn btn-sm btn-outline"
                    data-remove-row
                >
                    Hapus Baris
                </button>
            </td>
        </tr>
    </template>

    <script>
        (() => {
            const container = document.getElementById('detail-rows');
            const template = document.getElementById('detail-row-template');
            const addButton = document.getElementById('add-detail');

            let nextIndex = container.querySelectorAll('[data-detail-row]').length;

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex++)
                );

                const temporaryBody = document.createElement('tbody');
                temporaryBody.innerHTML = html.trim();

                container.appendChild(temporaryBody.firstElementChild);
            });

            container.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');

                if (!button) {
                    return;
                }

                const rows = container.querySelectorAll('[data-detail-row]');

                if (rows.length <= 1) {
                    alert('Minimal harus ada satu detail barang.');
                    return;
                }

                button.closest('[data-detail-row]').remove();
            });
        })();
    </script>
@endsection