@extends('gentelella::page')

@section('title', 'Edit Pesanan Penjualan')
@section('page_key', 'pesanan-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pesanan Penjualan > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit Pesanan Penjualan"
        pretitle="Sales Cycle"
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

        @php
            $defaultDetails = $pesanan->details->map(function ($detail) {
                return [
                    'sku' => $detail->sku,
                    'kuantitas' => $detail->kuantitas,
                    'harga_unit' => $detail->harga_unit,
                    'diskon' => $detail->diskon,
                ];
            })->all();

            $oldDetails = old('details', $defaultDetails);

            $nextDetailIndex = count($oldDetails)
                ? max(array_map('intval', array_keys($oldDetails))) + 1
                : 0;
        @endphp

        <form
            action="{{ route('pesanan-penjualan.update', $pesanan->no_pesanan) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_pesanan" class="form-label">
                    No. Pesanan <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="no_pesanan"
                    name="no_pesanan"
                    value="{{ old('no_pesanan', $pesanan->no_pesanan) }}"
                    class="form-control"
                    maxlength="255"
                    required
                >
                @error('no_pesanan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">
                    Tanggal <span class="required">*</span>
                </label>
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
                <label for="nama_pembeli" class="form-label">
                    Nama Pembeli <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="nama_pembeli"
                    name="nama_pembeli"
                    value="{{ old('nama_pembeli', $pesanan->nama_pembeli) }}"
                    class="form-control"
                    maxlength="255"
                    required
                >
                @error('nama_pembeli')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">
                    Alamat <span class="required">*</span>
                </label>
                <textarea
                    id="alamat"
                    name="alamat"
                    class="form-control"
                    rows="3"
                    required
                >{{ old('alamat', $pesanan->alamat) }}</textarea>
                @error('alamat')
                    <div class="form-error">{{ $message }}</div>
                @enderror
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
                        @foreach ($oldDetails as $index => $detail)
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
                                        required
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
                    href="{{ route('pesanan-penjualan.index') }}"
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
                    required
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
        document.addEventListener('DOMContentLoaded', function () {
            const detailRows = document.getElementById('detail-rows');
            const addButton = document.getElementById('add-detail');
            const template = document.getElementById('detail-row-template');

            let nextIndex = {{ $nextDetailIndex }};

            addButton.addEventListener('click', function () {
                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex)
                );

                detailRows.insertAdjacentHTML('beforeend', html);
                nextIndex++;
            });

            detailRows.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove-row]');

                if (!button) {
                    return;
                }

                const rows = detailRows.querySelectorAll('[data-detail-row]');

                if (rows.length <= 1) {
                    alert('Minimal harus ada satu detail barang.');
                    return;
                }

                button.closest('[data-detail-row]').remove();
            });
        });
    </script>
@endsection