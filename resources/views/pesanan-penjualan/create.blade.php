
@extends('gentelella::page')

@section('title', 'Buat Pesanan Penjualan')
@section('page_key', 'pesanan-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pesanan Penjualan > Tambah')

@section('content')
    @php
        $oldDetails = old('details', [
            [
                'sku' => '',
                'kuantitas' => 1,
                'harga_unit' => 0,
                'diskon' => 0,
            ],
        ]);

        if (empty($oldDetails)) {
            $oldDetails = [[
                'sku' => '',
                'kuantitas' => 1,
                'harga_unit' => 0,
                'diskon' => 0,
            ]];
        }

        $nextDetailIndex = max(array_map('intval', array_keys($oldDetails))) + 1;
    @endphp

    <style>
        .pesanan-penjualan-form-page .form-section {
            margin-bottom: 24px;
        }

        .pesanan-penjualan-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .pesanan-penjualan-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .pesanan-penjualan-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .pesanan-penjualan-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .pesanan-penjualan-form-page .required {
            color: #dc3545;
        }

        .pesanan-penjualan-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .pesanan-penjualan-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .pesanan-penjualan-form-page .section-description {
            margin: 0 0 18px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .pesanan-penjualan-form-page .full-width {
            grid-column: 1 / -1;
        }

        .pesanan-penjualan-form-page .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 16px;
        }

        .pesanan-penjualan-form-page .detail-table {
            width: 100%;
            min-width: 900px;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .pesanan-penjualan-form-page .detail-table th,
        .pesanan-penjualan-form-page .detail-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .pesanan-penjualan-form-page .detail-table th {
            text-align: center;
            white-space: normal;
        }

        .pesanan-penjualan-form-page .detail-table td:last-child {
            text-align: center;
        }

        .pesanan-penjualan-form-page .detail-table td:last-child .btn {
            white-space: nowrap;
        }

        .pesanan-penjualan-form-page .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #344256;
        }

        @media (max-width: 767px) {
            .pesanan-penjualan-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .pesanan-penjualan-form-page .full-width {
                grid-column: auto;
            }
        }
    </style>

    <div class="pesanan-penjualan-form-page">
        <x-gentelella::page-header
            title="Buat Pesanan Penjualan"
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

            @if (session('error'))
                <div class="banner banner-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('pesanan-penjualan.store') }}" method="POST" id="pesanan-form">
                @csrf

                <div class="form-section">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="no_pesanan" class="form-label">
                                No. Pesanan <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="no_pesanan"
                                name="no_pesanan"
                                value="{{ old('no_pesanan') }}"
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
                                value="{{ old('tanggal', date('Y-m-d')) }}"
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
                                value="{{ old('nama_pembeli') }}"
                                class="form-control"
                                maxlength="255"
                                required
                            >
                            @error('nama_pembeli')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ppn" class="form-label">
                                PPN (nominal)
                            </label>
                            <input
                                type="number"
                                id="ppn"
                                name="ppn"
                                value="{{ old('ppn', 0) }}"
                                class="form-control"
                                min="0"
                                step="0.01"
                                required
                            >
                            @error('ppn')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="alamat" class="form-label">
                                Alamat <span class="required">*</span>
                            </label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                class="form-control"
                                rows="3"
                                required
                            >{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Detail Barang</h3>
                    <p class="section-description">
                        Tambahkan barang yang dipesan, kemudian isi kuantitas, harga unit, dan diskon.
                    </p>

                    @foreach ($oldDetails as $index => $detail)
                        @error("details.$index.sku")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.kuantitas")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.harga_unit")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.diskon")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    @endforeach

                    <div class="table-responsive">
                        <table class="table table-striped detail-table">
                            <colgroup>
                                <col style="width: 30%">
                                <col style="width: 15%">
                                <col style="width: 20%">
                                <col style="width: 20%">
                                <col style="width: 15%">
                            </colgroup>
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
                                                Hapus
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
                        + Tambah Baris Barang
                    </button>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('pesanan-penjualan.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Pesanan
                    </button>
                </div>
            </form>

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
                            Hapus
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
        </x-gentelella::card>
    </div>
@endsection
