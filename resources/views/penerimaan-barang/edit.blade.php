
@extends('gentelella::page')

@section('title', 'Edit Penerimaan Barang')
@section('page_key', 'penerimaan-barang')
@section('breadcrumb', 'Home > Purchasing Cycle > Penerimaan Barang > Edit')

@section('content')
    @php
        $details = old(
            'details',
            $penerimaan->details->map(fn ($detail) => [
                'sku' => $detail->sku,
                'kuantitas' => $detail->kuantitas,
                'kondisi_barang' => $detail->kondisi_barang,
                'keterangan' => $detail->keterangan,
            ])->all()
        );

        if (empty($details)) {
            $details = [[
                'sku' => '',
                'kuantitas' => 1,
                'kondisi_barang' => '',
                'keterangan' => '',
            ]];
        }

        $orderOptions = $pesanans->map(function ($pesanan) {
            return [
                'value' => $pesanan->no_order_pembelian,
                'items' => $pesanan->details->map(function ($detail) {
                    return [
                        'sku' => $detail->sku,
                        'label' => $detail->sku . ' — ' .
                            ($detail->barang?->nama_barang ?? 'Barang tidak ditemukan'),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    @endphp

    <x-gentelella::page-header
        title="Edit Penerimaan Barang"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Informasi Penerimaan">
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

        <form
            action="{{ route('penerimaan-barang.update', $penerimaan->no_penerimaan) }}"
            method="POST"
            id="penerimaan-form"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_penerimaan" class="form-label">
                    No. Penerimaan <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="no_penerimaan"
                    name="no_penerimaan"
                    value="{{ old('no_penerimaan', $penerimaan->no_penerimaan) }}"
                    class="form-control"
                    maxlength="255"
                    required
                >
                @error('no_penerimaan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">
                    Tanggal Penerimaan <span class="required">*</span>
                </label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="{{ old('tanggal', $penerimaan->tanggal->format('Y-m-d')) }}"
                    class="form-control"
                    required
                >
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="no_order_pembelian" class="form-label">
                    Pesanan Pembelian <span class="required">*</span>
                </label>
                <select
                    id="no_order_pembelian"
                    name="no_order_pembelian"
                    class="form-control"
                    required
                >
                    <option value="">Pilih pesanan pembelian</option>
                    @foreach ($pesanans as $pesanan)
                        <option
                            value="{{ $pesanan->no_order_pembelian }}"
                            @selected(old('no_order_pembelian', $penerimaan->no_order_pembelian) == $pesanan->no_order_pembelian)
                        >
                            {{ $pesanan->no_order_pembelian }}
                            — {{ $pesanan->vendor?->nama_vendor ?? 'Tanpa vendor' }}
                        </option>
                    @endforeach
                </select>
                @error('no_order_pembelian')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <h3>Detail Barang Diterima</h3>
            <p id="order-items-help">
                Barang yang tersedia berasal dari detail pesanan pembelian yang dipilih.
            </p>

            <div style="overflow-x: auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang / SKU</th>
                            <th>Kuantitas Diterima</th>
                            <th>Kondisi Barang</th>
                            <th>Keterangan</th>
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
                                        required
                                    >
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        name="details[{{ $index }}][kondisi_barang]"
                                        class="form-control"
                                        maxlength="255"
                                        value="{{ $detail['kondisi_barang'] ?? '' }}"
                                        placeholder="Contoh: Baik"
                                        required
                                    >
                                </td>

                                <td>
                                    <textarea
                                        name="details[{{ $index }}][keterangan]"
                                        class="form-control"
                                        rows="2"
                                    >{{ $detail['keterangan'] ?? '' }}</textarea>
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
                    href="{{ route('penerimaan-barang.index') }}"
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
                        required
                    >
                </td>

                <td>
                    <input
                        type="text"
                        name="details[__INDEX__][kondisi_barang]"
                        class="form-control"
                        maxlength="255"
                        placeholder="Contoh: Baik"
                        required
                    >
                </td>

                <td>
                    <textarea
                        name="details[__INDEX__][keterangan]"
                        class="form-control"
                        rows="2"
                    ></textarea>
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
                const orderSelect = document.getElementById('no_order_pembelian');
                const detailRows = document.getElementById('detail-rows');
                const template = document.getElementById('detail-row-template');
                const addButton = document.getElementById('add-detail');
                const helpText = document.getElementById('order-items-help');

                const orderOptions = {{ \Illuminate\Support\Js::from($orderOptions) }};
                let nextIndex = {{ max(array_map('intval', array_keys($details))) + 1 }};

                function refreshSkuOptions() {
                    const selectedOrder = orderOptions.find(
                        order => order.value === orderSelect.value
                    );

                    const items = selectedOrder ? selectedOrder.items : [];

                    detailRows.querySelectorAll('[data-sku-select]').forEach(select => {
                        const previousSku = select.value || select.dataset.selectedSku || '';

                        select.replaceChildren(new Option('Pilih barang', ''));

                        items.forEach(item => {
                            select.add(new Option(item.label, item.sku));
                        });

                        select.value = items.some(item => item.sku === previousSku)
                            ? previousSku
                            : '';

                        select.dataset.selectedSku = '';
                        select.disabled = items.length === 0;
                    });

                    helpText.textContent = !orderSelect.value
                        ? 'Pilih pesanan pembelian terlebih dahulu untuk melihat barangnya.'
                        : items.length === 0
                            ? 'Pesanan ini belum memiliki detail barang.'
                            : 'Barang yang tersedia berasal dari detail pesanan pembelian yang dipilih.';
                }

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
                        row.querySelector('[data-sku-select]').dataset.selectedSku = '';
                        row.querySelector('[name$="[kuantitas]"]').value = 1;
                        row.querySelector('[name$="[kondisi_barang]"]').value = '';
                        row.querySelector('[name$="[keterangan]"]').value = '';
                    }

                    refreshSkuOptions();
                });

                orderSelect.addEventListener('change', refreshSkuOptions);

                refreshSkuOptions();
            });
        </script>
    </x-gentelella::card>
@endsection
