
@extends('gentelella::page')

@section('title', 'Buat Faktur Penjualan')
@section('page_key', 'faktur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Faktur Penjualan > Tambah')

@section('content')
    <x-gentelella::page-header
        title="Buat Faktur Penjualan"
        pretitle="Sales Cycle"
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

        @php
            $oldDetails = old('details', [[
                'sku' => '',
                'kuantitas' => 1,
                'harga_unit' => 0,
                'diskon' => 0,
            ]]);

            $nextIndex = count($oldDetails)
                ? max(array_map('intval', array_keys($oldDetails))) + 1
                : 0;

            $pengirimanItems = $pengirimans->mapWithKeys(function ($pengiriman) {
                $pesananDetails = $pengiriman->pesananPenjualan?->details
                    ?? collect();

                $items = $pengiriman->details
                    ->groupBy('sku')
                    ->map(function ($rows, $sku) use ($pesananDetails) {
                        $sourceDetail = $pesananDetails->firstWhere('sku', $sku);

                        return [
                            'sku' => $sku,
                            'nama_barang' => $rows->first()->barang?->nama_barang ?? '-',
                            'available_quantity' => (int) $rows->sum('total'),
                            'unit_price' => (float) ($sourceDetail?->harga_unit ?? 0),
                        ];
                    })
                    ->values();

                return [
                    (string) $pengiriman->id_pengiriman_pesanan => $items,
                ];
            });
        @endphp

        <form action="{{ route('faktur-penjualan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="no_faktur" class="form-label">
                    No. Faktur <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="no_faktur"
                    name="no_faktur"
                    class="form-control"
                    value="{{ old('no_faktur') }}"
                    maxlength="255"
                    required
                >
                @error('no_faktur')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="id_pengiriman_pesanan" class="form-label">
                    ID Pengiriman <span class="required">*</span>
                </label>
                <select
                    id="id_pengiriman_pesanan"
                    name="id_pengiriman_pesanan"
                    class="form-control"
                    required
                >
                    <option value="">Pilih pengiriman pesanan</option>
                    @foreach ($pengirimans as $pengiriman)
                        <option
                            value="{{ $pengiriman->id_pengiriman_pesanan }}"
                            @selected(old('id_pengiriman_pesanan') == $pengiriman->id_pengiriman_pesanan)
                        >
                            {{ $pengiriman->id_pengiriman_pesanan }}
                            — {{ $pengiriman->no_pesanan }}
                            — {{ $pengiriman->pesananPenjualan?->nama_pembeli ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('id_pengiriman_pesanan')
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
                    class="form-control"
                    value="{{ old('tanggal', date('Y-m-d')) }}"
                    required
                >
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Pembeli <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    class="form-control"
                    value="{{ old('nama') }}"
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
                    class="form-control"
                    value="{{ old('no_telp') }}"
                    maxlength="255"
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
                    class="form-control"
                    value="{{ old('jangka_waktu_pembayaran', '30 hari') }}"
                    maxlength="255"
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
                    class="form-control"
                    value="{{ old('ppn', 0) }}"
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
                                        data-sku-select
                                        data-selected-sku="{{ $detail['sku'] ?? '' }}"
                                        required
                                    >
                                        <option value="">Pilih pengiriman terlebih dahulu</option>
                                    </select>
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="details[{{ $index }}][kuantitas]"
                                        class="form-control"
                                        data-quantity-input
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
                                        data-price-input
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
                    Simpan Faktur
                </button>
                <a
                    href="{{ route('faktur-penjualan.index') }}"
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
                    data-quantity-input
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
                    data-price-input
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
            const deliverySelect = document.getElementById('id_pengiriman_pesanan');
            const detailRows = document.getElementById('detail-rows');
            const addButton = document.getElementById('add-detail');
            const template = document.getElementById('detail-row-template');

            const shipmentItems = @json($pengirimanItems);
            let nextIndex = {{ $nextIndex }};

            function updateRow(select, fillPrice = false) {
                const row = select.closest('[data-detail-row]');
                const quantityInput = row.querySelector('[data-quantity-input]');
                const priceInput = row.querySelector('[data-price-input]');
                const option = select.selectedOptions[0];

                if (!option || !option.value) {
                    quantityInput.removeAttribute('max');
                    return;
                }

                const available = Number(option.dataset.availableQuantity || 0);

                quantityInput.max = available;

                // Saat barang dipilih, isi kuantitas sesuai jumlah yang dikirim.
                if (fillPrice) {
                    quantityInput.value = available;
                } else if (Number(quantityInput.value) > available) {
                    quantityInput.value = available;
                }

                // Isi harga unit dari detail pesanan penjualan.
                if (fillPrice || Number(priceInput.value) === 0) {
                    priceInput.value = option.dataset.unitPrice || 0;
                }
            }

            function populateSKUSelect(select, selectedSKU = '') {
                const items = shipmentItems[deliverySelect.value] || [];
                select.replaceChildren(new Option('Pilih barang', ''));

                items.forEach(function (item) {
                    const option = new Option(
                        `${item.sku} — ${item.nama_barang} (dikirim: ${item.available_quantity})`,
                        item.sku
                    );

                    option.dataset.availableQuantity = item.available_quantity;
                    option.dataset.unitPrice = item.unit_price;
                    select.add(option);
                });

                select.value = items.some(item => item.sku === selectedSKU)
                    ? selectedSKU
                    : '';

                select.disabled = !deliverySelect.value || items.length === 0;

                if (!select.value) {
                    const row = select.closest('[data-detail-row]');
                    row.querySelector('[data-quantity-input]').removeAttribute('max');

                    if (selectedSKU) {
                        row.querySelector('[data-quantity-input]').value = 1;
                        row.querySelector('[data-price-input]').value = 0;
                    }
                } else {
                    updateRow(select);
                }
            }

            function populateAllRows() {
                detailRows.querySelectorAll('[data-sku-select]').forEach(function (select) {
                    const selectedSKU = select.value || select.dataset.selectedSku || '';
                    populateSKUSelect(select, selectedSKU);
                    select.dataset.selectedSku = '';
                });
            }

            deliverySelect.addEventListener('change', function () {
                populateAllRows();
            });

            detailRows.addEventListener('change', function (event) {
                if (event.target.matches('[data-sku-select]')) {
                    updateRow(event.target, true);
                }
            });

            addButton.addEventListener('click', function () {
                if (!deliverySelect.value) {
                    alert('Pilih pengiriman pesanan terlebih dahulu.');
                    return;
                }

                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex)
                );

                detailRows.insertAdjacentHTML('beforeend', html);

                const newSelect = detailRows.lastElementChild
                    .querySelector('[data-sku-select]');

                populateSKUSelect(newSelect);
                nextIndex++;
            });

            detailRows.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove-row]');

                if (!button) {
                    return;
                }

                if (detailRows.querySelectorAll('[data-detail-row]').length <= 1) {
                    alert('Minimal harus ada satu detail barang.');
                    return;
                }

                button.closest('[data-detail-row]').remove();
            });

            populateAllRows();
        });
    </script>
@endsection
