
@extends('gentelella::page')

@section('title', 'Tambah Penerimaan Barang')
@section('page_key', 'penerimaan-barang')
@section('breadcrumb', 'Home > Purchasing Cycle > Penerimaan Barang > Tambah')

@section('content')
    @php
        $details = old('details', [[
            'sku' => '',
            'kuantitas' => 1,
            'kondisi_barang' => '',
            'keterangan' => '',
        ]]);
    @endphp

    <div class="penerimaan-form-page">
        <x-gentelella::page-header
            title="Tambah Penerimaan Barang"
            pretitle="Purchasing Cycle"
        />

        <style>
            .penerimaan-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .penerimaan-form-grid .form-group {
                min-width: 0;
                margin-bottom: 0;
            }

            .penerimaan-form-page .form-control {
                width: 100%;
                box-sizing: border-box;
            }

            .penerimaan-detail-section {
                margin-top: 28px;
                padding-top: 24px;
                border-top: 1px solid var(--border-color);
            }

            .penerimaan-detail-heading {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 16px;
            }

            .penerimaan-detail-heading h3 {
                margin: 0 0 4px;
                font-size: 17px;
                font-weight: 600;
            }

            .penerimaan-detail-table {
                width: 100%;
                min-width: 880px;
                table-layout: fixed;
            }

            .penerimaan-detail-table th,
            .penerimaan-detail-table td {
                padding: 12px;
                vertical-align: middle;
            }

            .penerimaan-detail-table th {
                text-align: center;
            }

            .penerimaan-detail-table th:nth-child(1) {
                width: 30%;
            }

            .penerimaan-detail-table th:nth-child(2) {
                width: 17%;
            }

            .penerimaan-detail-table th:nth-child(3) {
                width: 20%;
            }

            .penerimaan-detail-table th:nth-child(4) {
                width: 21%;
            }

            .penerimaan-detail-table th:nth-child(5) {
                width: 12%;
            }

            .penerimaan-form-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }

            @media (max-width: 700px) {
                .penerimaan-form-grid {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 16px;
                }

                .penerimaan-form-actions {
                    flex-wrap: wrap;
                }
            }
        </style>

        <x-gentelella::card title="Informasi Penerimaan">
            <p style="color:var(--text-muted); font-size:13px; margin:0 0 24px;">
                Catat penerimaan barang berdasarkan pesanan pembelian yang tersedia.
                Kolom bertanda <span class="required">*</span> wajib diisi.
            </p>

            @if ($errors->any())
                <div class="banner banner-danger" role="alert" style="margin-bottom:24px;">
                    <strong>Periksa kembali input berikut:</strong>
                    <ul style="margin:8px 0 0; padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="banner banner-danger" role="alert" style="margin-bottom:24px;">
                    {{ session('error') }}
                </div>
            @endif

            @if (empty($orderOptions))
                <div class="banner banner-danger" role="alert">
                    Tidak ada barang yang masih bisa diterima dari Pesanan Pembelian yang tersedia.
                </div>

                <a
                    href="{{ route('pesanan-pembelian.index') }}"
                    class="btn btn-outline"
                >
                    Buka Pesanan Pembelian
                </a>
            @else
                <form
                    action="{{ route('penerimaan-barang.store') }}"
                    method="POST"
                    id="penerimaan-form"
                >
                    @csrf

                    <div class="penerimaan-form-grid">
                        <div class="form-group">
                            <label for="no_penerimaan" class="form-label">
                                No. Penerimaan <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="no_penerimaan"
                                name="no_penerimaan"
                                value="{{ old('no_penerimaan', $nextNoPenerimaan) }}"
                                class="form-control @error('no_penerimaan') is-invalid @enderror"
                                maxlength="255"
                                placeholder="Masukkan nomor penerimaan"
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
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                class="form-control @error('tanggal') is-invalid @enderror"
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
                                class="form-control @error('no_order_pembelian') is-invalid @enderror"
                                required
                            >
                                <option value="">Pilih pesanan pembelian</option>

                                @foreach ($orderOptions as $pesanan)
                                    <option
                                        value="{{ $pesanan['value'] }}"
                                        @selected(old('no_order_pembelian') == $pesanan['value'])
                                    >
                                        {{ $pesanan['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            @error('no_order_pembelian')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="penerimaan-detail-section">
                        <div class="penerimaan-detail-heading">
                            <div>
                                <h3>Detail Barang Diterima</h3>
                                <p
                                    id="order-items-help"
                                    style="color:var(--text-muted); font-size:13px; margin:0;"
                                >
                                    Pilih pesanan pembelian terlebih dahulu untuk melihat barangnya.
                                </p>
                            </div>

                            <button
                                type="button"
                                id="add-detail"
                                class="btn btn-outline"
                            >
                                + Tambah Baris Barang
                            </button>
                        </div>

                        <div style="overflow-x:auto;">
                            <table class="table penerimaan-detail-table">
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

                                            <td style="text-align:center;">
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
                    </div>

                    <div class="penerimaan-form-actions">
                        <a
                            href="{{ route('penerimaan-barang.index') }}"
                            class="btn btn-outline"
                        >
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Penerimaan
                        </button>
                    </div>
                </form>
            @endif
        </x-gentelella::card>
    </div>

    @if (!empty($orderOptions))
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

                <td style="text-align:center;">
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
                const orderSelect = document.getElementById('no_order_pembelian');
                const detailRows = document.getElementById('detail-rows');
                const template = document.getElementById('detail-row-template');
                const addButton = document.getElementById('add-detail');
                const helpText = document.getElementById('order-items-help');

                if (!orderSelect || !detailRows || !template || !addButton || !helpText) {
                    return;
                }

                const orderOptions = {{ \Illuminate\Support\Js::from($orderOptions) }};

                let nextIndex = {{ max(array_keys($details)) + 1 }};

                function refreshSkuOptions() {
                    const selectedOrder = orderOptions.find(
                        order => order.value === orderSelect.value
                    );

                    const items = selectedOrder ? selectedOrder.items : [];

                    detailRows.querySelectorAll('[data-sku-select]').forEach(select => {
                        const previousSku = select.value || select.dataset.selectedSku || '';

                        select.replaceChildren(new Option('Pilih barang', ''));

                        items.forEach(item => {
                            const option = new Option(item.label, item.sku);
                            option.dataset.availableQuantity = String(item.kuantitas);
                            select.add(option);
                        });

                        select.value = items.some(item => item.sku === previousSku)
                            ? previousSku
                            : '';
                        const row = select.closest('[data-detail-row]');
                        const quantity = row.querySelector('[name$="[kuantitas]"]');
                        const selectedOption = select.selectedOptions[0];

                        if (selectedOption?.value) {
                            quantity.max = selectedOption.dataset.availableQuantity;
                        } else {
                            quantity.removeAttribute('max');
                        }

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

                detailRows.addEventListener('change', function (event) {
                    if (!event.target.matches('[data-sku-select]')) {
                        return;
                    }

                    const select = event.target;
                    const option = select.selectedOptions[0];
                    const row = select.closest('[data-detail-row]');
                    const quantity = row.querySelector('[name$="[kuantitas]"]');

                    if (option?.value) {
                        quantity.max = option.dataset.availableQuantity;
                        quantity.value = option.dataset.availableQuantity;
                    } else {
                        quantity.removeAttribute('max');
                        quantity.value = 1;
                    }
                });

                orderSelect.addEventListener('change', refreshSkuOptions);

                refreshSkuOptions();
            });
        </script>
    @endif
@endsection
