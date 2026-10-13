@extends('gentelella::page')

@section('title', 'Tambah Pengiriman Pesanan')
@section('page_key', 'pengiriman-pesanan')
@section('breadcrumb', 'Home > Sales Cycle > Pengiriman Pesanan > Tambah')

@section('content')
    <x-gentelella::page-header
        title="Tambah Pengiriman Pesanan"
        pretitle="Sales Cycle"
    />

    <x-gentelella::card title="Informasi Pengiriman">
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
            $oldDetails = old('details', [
                ['sku' => '', 'keterangan' => '', 'total' => 1],
            ]);

            $nextIndex = count($oldDetails)
                ? max(array_map('intval', array_keys($oldDetails))) + 1
                : 0;

            $orderItems = $pesanans->mapWithKeys(function ($pesanan) {
                $items = $pesanan->details
                    ->map(function ($detail) {
                        return [
                            'sku' => $detail->sku,
                            'nama_barang' => $detail->barang?->nama_barang ?? '-',
                        ];
                    })
                    ->unique('sku')
                    ->values();

                return [$pesanan->no_pesanan => $items];
            });
        @endphp

        <form action="{{ route('pengiriman-pesanan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="no_pesanan" class="form-label">
                    No. Pesanan <span class="required">*</span>
                </label>

                <select
                    id="no_pesanan"
                    name="no_pesanan"
                    class="form-control"
                    required
                >
                    <option value="">Pilih pesanan penjualan</option>

                    @foreach ($pesanans as $pesanan)
                        <option
                            value="{{ $pesanan->no_pesanan }}"
                            @selected(old('no_pesanan') == $pesanan->no_pesanan)
                        >
                            {{ $pesanan->no_pesanan }}
                            — {{ $pesanan->nama_pembeli }}
                        </option>
                    @endforeach
                </select>

                @error('no_pesanan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">
                    Tanggal Pengiriman <span class="required">*</span>
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
                <label for="nama_pengirim" class="form-label">
                    Nama Pengirim <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="nama_pengirim"
                    name="nama_pengirim"
                    class="form-control"
                    value="{{ old('nama_pengirim') }}"
                    maxlength="255"
                    required
                >

                @error('nama_pengirim')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <h3>Detail Pengiriman</h3>

            <div style="overflow-x: auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang / SKU</th>
                            <th>Keterangan</th>
                            <th>Jumlah Dikirim</th>
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
                                        <option value="">Pilih barang</option>
                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        name="details[{{ $index }}][keterangan]"
                                        class="form-control"
                                        value="{{ $detail['keterangan'] ?? '' }}"
                                        placeholder="Keterangan (opsional)"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="details[{{ $index }}][total]"
                                        class="form-control"
                                        min="1"
                                        value="{{ $detail['total'] ?? 1 }}"
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
                    Simpan Pengiriman
                </button>

                <a
                    href="{{ route('pengiriman-pesanan.index') }}"
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
                    type="text"
                    name="details[__INDEX__][keterangan]"
                    class="form-control"
                    placeholder="Keterangan (opsional)"
                >
            </td>

            <td>
                <input
                    type="number"
                    name="details[__INDEX__][total]"
                    class="form-control"
                    min="1"
                    value="1"
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
            const orderSelect = document.getElementById('no_pesanan');
            const detailRows = document.getElementById('detail-rows');
            const addButton = document.getElementById('add-detail');
            const template = document.getElementById('detail-row-template');

            const orderItems = @json($orderItems);
            let nextIndex = {{ $nextIndex }};

            function populateSKUSelect(select, selectedSKU = '') {
                const items = orderItems[orderSelect.value] || [];

                select.replaceChildren(new Option('Pilih barang', ''));

                items.forEach(function (item) {
                    const option = new Option(
                        item.sku + ' — ' + item.nama_barang,
                        item.sku
                    );

                    select.add(option);
                });

                if (items.some(item => item.sku === selectedSKU)) {
                    select.value = selectedSKU;
                } else {
                    select.value = '';
                }

                select.disabled = !orderSelect.value || items.length === 0;
            }

            function populateAllRows() {
                detailRows.querySelectorAll('[data-sku-select]').forEach(
                    function (select) {
                        const selectedSKU =
                            select.value || select.dataset.selectedSku || '';

                        populateSKUSelect(select, selectedSKU);
                        select.dataset.selectedSku = '';
                    }
                );
            }

            orderSelect.addEventListener('change', populateAllRows);

            addButton.addEventListener('click', function () {
                if (!orderSelect.value) {
                    alert('Pilih pesanan penjualan terlebih dahulu.');
                    return;
                }

                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex)
                );

                detailRows.insertAdjacentHTML('beforeend', html);

                const newRow = detailRows.lastElementChild;
                populateSKUSelect(newRow.querySelector('[data-sku-select]'));

                nextIndex++;
            });

            detailRows.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove-row]');

                if (!button) {
                    return;
                }

                const rows = detailRows.querySelectorAll('[data-detail-row]');

                if (rows.length <= 1) {
                    alert('Minimal harus ada satu detail pengiriman.');
                    return;
                }

                button.closest('[data-detail-row]').remove();
            });

            populateAllRows();
        });
    </script>
@endsection