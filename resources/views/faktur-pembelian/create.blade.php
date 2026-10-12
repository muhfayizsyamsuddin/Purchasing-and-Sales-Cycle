
@extends('gentelella::page')

@section('title', 'Tambah Faktur Pembelian')
@section('page_key', 'faktur-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Faktur Pembelian > Tambah')

@section('content')
    @php
        $details = old('details', [[
            'sku' => '',
            'kuantitas' => 1,
            'harga_unit' => 0,
            'diskon' => 0,
        ]]);

        $penerimaanOptions = $penerimaans->map(function ($penerimaan) {

            return [
                'value' => $penerimaan->no_penerimaan,
                'label' => $penerimaan->no_penerimaan
                    . ' — '
                    . ($penerimaan->pesanan?->no_order_pembelian ?? '-')
                    . ' — '
                    . ($penerimaan->pesanan?->vendor?->nama_vendor ?? '-'),

                'items' => $penerimaan->details->map(function ($detail) use ($penerimaan) {
                    $pesananDetail = \Illuminate\Support\Facades\DB::table('detail_m_pesanan_pembelian')
                        ->where('no_order_pembelian', $penerimaan->no_order_pembelian)
                        ->where('sku', $detail->sku)
                        ->first();

                    return [
                        'sku' => $detail->sku,
                        'label' => $detail->sku
                            . ' — '
                            . ($detail->barang?->nama_barang ?? 'Barang tidak ditemukan'),
                        'kuantitas' => (int) $detail->kuantitas,
                        'harga_unit' => (float) ($pesananDetail?->harga_unit ?? 0),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    @endphp

    <x-gentelella::page-header
        title="Tambah Faktur Pembelian"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Informasi Faktur">
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

        @if ($penerimaans->isEmpty())
            <div class="banner banner-danger" role="alert">
                Belum ada Penerimaan Barang. Catat penerimaan terlebih dahulu
                sebelum membuat faktur pembelian.
            </div>

            <a
                href="{{ route('penerimaan-barang.index') }}"
                class="btn btn-outline"
            >
                Buka Penerimaan Barang
            </a>
        @else
            <form action="{{ route('faktur-pembelian.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="no_faktur" class="form-label">
                        No. Faktur <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="no_faktur"
                        name="no_faktur"
                        value="{{ old('no_faktur') }}"
                        class="form-control"
                        maxlength="255"
                        required
                    >
                    @error('no_faktur')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal" class="form-label">
                        Tanggal Faktur <span class="required">*</span>
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
                    <label for="no_penerimaan" class="form-label">
                        No. Penerimaan <span class="required">*</span>
                    </label>
                    <select
                        id="no_penerimaan"
                        name="no_penerimaan"
                        class="form-control"
                        required
                    >
                        <option value="">Pilih penerimaan barang</option>
                        @foreach ($penerimaanOptions as $penerimaan)
                            <option
                                value="{{ $penerimaan['value'] }}"
                                @selected(old('no_penerimaan') === $penerimaan['value'])
                            >
                                {{ $penerimaan['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('no_penerimaan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama" class="form-label">
                        Nama Vendor / Penagih <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama') }}"
                        class="form-control"
                        maxlength="255"
                        required
                    >
                    @error('nama')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="no_telp" class="form-label">
                        No. Telepon <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="no_telp"
                        name="no_telp"
                        value="{{ old('no_telp') }}"
                        class="form-control"
                        maxlength="50"
                        required
                    >
                    @error('no_telp')
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
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jangka_waktu_pembayaran" class="form-label">
                        Jangka Waktu Pembayaran <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="jangka_waktu_pembayaran"
                        name="jangka_waktu_pembayaran"
                        value="{{ old('jangka_waktu_pembayaran', '30 hari') }}"
                        class="form-control"
                        maxlength="255"
                        placeholder="Contoh: Tunai, 14 hari, 30 hari"
                        required
                    >
                    @error('jangka_waktu_pembayaran')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="ppn" class="form-label">PPN (nominal)</label>
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

                <hr>

                <h3>Detail Faktur</h3>
                <p id="receipt-items-help">
                    Pilih penerimaan barang untuk memuat daftar barangnya.
                    Harga satuan awal mengikuti harga pada Pesanan Pembelian.
                </p>

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
                                            data-sku-select
                                            data-selected-sku="{{ $detail['sku'] ?? '' }}"
                                            required
                                        >
                                            <option value="">Pilih barang</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input
                                            type="number"
                                            name="details[{{ $index }}][kuantitas]"
                                            class="form-control"
                                            min="1"
                                            step="1"
                                            value="{{ $detail['kuantitas'] ?? 1 }}"
                                            data-quantity-input
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
                                            data-price-input
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
                                            data-discount
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
                        Simpan Faktur
                    </button>

                    <a
                        href="{{ route('faktur-pembelian.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                </div>
            </form>

            <template id="detail-row-template">
                <tr data-detail-row>
                    <td>
                        <select
                            name="details[__INDEX__][sku]"
                            class="form-control"
                            data-sku-select
                            required
                        >
                            <option value="">Pilih barang</option>
                        </select>
                    </td>

                    <td>
                        <input
                            type="number"
                            name="details[__INDEX__][kuantitas]"
                            class="form-control"
                            min="1"
                            step="1"
                            value="1"
                            data-quantity-input
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
                            data-price-input
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
                            data-discount
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
                    const receiptSelect = document.getElementById('no_penerimaan');
                    const detailRows = document.getElementById('detail-rows');
                    const template = document.getElementById('detail-row-template');
                    const addButton = document.getElementById('add-detail');
                    const helpText = document.getElementById('receipt-items-help');

                    const receiptOptions = {{ \Illuminate\Support\Js::from($penerimaanOptions) }};
                    let nextIndex = {{ max(array_map('intval', array_keys($details))) + 1 }};

                    function refreshSkuOptions() {
                        const selectedReceipt = receiptOptions.find(
                            receipt => receipt.value === receiptSelect.value
                        );

                        const items = selectedReceipt ? selectedReceipt.items : [];

                        detailRows.querySelectorAll('[data-detail-row]').forEach(row => {
                            const select = row.querySelector('[data-sku-select]');
                            const quantity = row.querySelector('[data-quantity-input]');
                            const price = row.querySelector('[data-price-input]');
                            const previousSku = select.value || select.dataset.selectedSku || '';

                            select.replaceChildren(new Option('Pilih barang', ''));

                            items.forEach(item => {
                                const option = document.createElement('option');

                                option.textContent = item.label;
                                option.value = String(item.sku);
                                option.dataset.availableQuantity = String(item.kuantitas);
                                option.dataset.unitPrice = String(item.harga_unit);

                                select.appendChild(option);
                            });

                            select.value = items.some(item => item.sku === previousSku)
                                ? previousSku
                                : '';

                            select.disabled = items.length === 0;
                            select.dataset.selectedSku = '';

                            const selectedOption = select.selectedOptions[0];

                            if (selectedOption && selectedOption.value) {
                                quantity.max = selectedOption.dataset.availableQuantity;

                                if (!price.dataset.userEdited) {
                                    price.value = selectedOption.dataset.unitPrice;
                                }
                            } else {
                                quantity.removeAttribute('max');
                            }
                        });

                        helpText.textContent = !receiptSelect.value
                            ? 'Pilih penerimaan barang untuk memuat daftar barangnya.'
                            : items.length === 0
                                ? 'Penerimaan ini belum memiliki detail barang.'
                                : 'Kuantitas faktur tidak boleh melebihi jumlah yang diterima. Harga awal mengikuti Pesanan Pembelian.';
                    }

                    detailRows.addEventListener('change', function (event) {
                        if (event.target.matches('[data-sku-select]')) {
                            const row = event.target.closest('[data-detail-row]');
                            const option = event.target.selectedOptions[0];
                            const quantity = row.querySelector('[data-quantity-input]');
                            const price = row.querySelector('[data-price-input]');

                            if (option && option.value) {
                                quantity.max = option.dataset.availableQuantity;
                                quantity.value = option.dataset.availableQuantity;

                                price.value = option.dataset.unitPrice;
                                price.dataset.userEdited = '';
                            }
                        }
                    });

                    detailRows.addEventListener('input', function (event) {
                        if (event.target.matches('[data-price-input]')) {
                            event.target.dataset.userEdited = 'true';
                        }
                    });

                    function addDetailRow() {
                        const html = template.innerHTML.replace(
                            /__INDEX__/g,
                            String(nextIndex++)
                        );

                        detailRows.insertAdjacentHTML('beforeend', html);
                        refreshSkuOptions();
                    }

                    addButton.addEventListener('click', addDetailRow);

                    detailRows.addEventListener('click', function (event) {
                        const button = event.target.closest('[data-remove-row]');

                        if (!button) {
                            return;
                        }

                        const rows = detailRows.querySelectorAll('[data-detail-row]');

                        if (rows.length > 1) {
                            button.closest('[data-detail-row]').remove();
                        } else {
                            const row = button.closest('[data-detail-row]');

                            row.querySelector('[data-sku-select]').value = '';
                            row.querySelector('[data-quantity-input]').value = 1;
                            row.querySelector('[data-price-input]').value = 0;
                            row.querySelector('[data-discount]').value = 0;
                        }

                        refreshSkuOptions();
                    });

                    receiptSelect.addEventListener('change', refreshSkuOptions);

                    refreshSkuOptions();
                });
            </script>
        @endif
    </x-gentelella::card>
@endsection
