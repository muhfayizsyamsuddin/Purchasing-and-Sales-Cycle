
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

    <style>
        .penerimaan-form-page .form-section {
            margin-bottom: 24px;
        }

        .penerimaan-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .penerimaan-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .penerimaan-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .penerimaan-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .penerimaan-form-page .required {
            color: #dc3545;
        }

        .penerimaan-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .penerimaan-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .penerimaan-form-page .section-description {
            margin: 0 0 18px;
            color: #73808c;
            font-size: 13px;
        }

        .penerimaan-form-page .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 16px;
        }

        .penerimaan-form-page .penerimaan-detail-table {
            width: 100%;
            min-width: 880px;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .penerimaan-form-page .penerimaan-detail-table th,
        .penerimaan-form-page .penerimaan-detail-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .penerimaan-form-page .penerimaan-detail-table th {
            text-align: center;
            white-space: normal;
        }

        .penerimaan-form-page .penerimaan-detail-table td:last-child {
            text-align: center;
        }

        .penerimaan-form-page .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e9ecef;
        }

        .penerimaan-form-page .form-help {
            margin-top: 8px;
            color: #73808c;
            font-size: 12px;
        }

        @media (max-width: 767px) {
            .penerimaan-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="penerimaan-form-page">
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

                <div class="form-section">
                    <div class="form-grid">
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
                                class="form-control"
                                value="{{ old('tanggal', $penerimaan->tanggal?->format('Y-m-d')) }}"
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
                            <div class="form-help">
                                Pilih pesanan pembelian untuk menentukan barang yang tersedia.
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Detail Barang Diterima</h3>
                    <p class="section-description" id="order-items-help">
                        Barang yang tersedia berasal dari detail pesanan pembelian yang dipilih.
                    </p>

                    @foreach ($details as $index => $detail)
                        @error("details.$index.sku")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.kuantitas")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.kondisi_barang")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error("details.$index.keterangan")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    @endforeach

                    <div class="table-responsive">
                        <table class="table table-striped penerimaan-detail-table">
                            <colgroup>
                                <col style="width: 26%">
                                <col style="width: 16%">
                                <col style="width: 20%">
                                <col style="width: 24%">
                                <col style="width: 14%">
                            </colgroup>
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
                        href="{{ route('penerimaan-barang.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
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

                    const orderOptions = {{ \Illuminate\Support\Js::from($orderOptions) }};
                    let nextIndex = {{ max(array_map('intval', array_keys($details))) + 1 }};

                    function refreshSkuOptions() {
                        const selectedOrder = orderOptions.find(
                            order => String(order.value) === orderSelect.value
                        );

                        const items = selectedOrder ? selectedOrder.items : [];

                        detailRows.querySelectorAll('[data-sku-select]').forEach(select => {
                            const previousSku = select.value || select.dataset.selectedSku || '';

                            select.replaceChildren(new Option('Pilih barang', ''));

                            items.forEach(item => {
                                select.add(new Option(item.label, item.sku));
                            });

                            select.value = items.some(
                                item => String(item.sku) === String(previousSku)
                            ) ? previousSku : '';

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
                        const row = button.closest('[data-detail-row]');

                        if (rows.length > 1) {
                            row.remove();
                        } else {
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
    </div>
@endsection
