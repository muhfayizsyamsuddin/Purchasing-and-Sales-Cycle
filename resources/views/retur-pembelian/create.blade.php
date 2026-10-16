
@extends('gentelella::page')

@section('title', 'Tambah Retur Pembelian')
@section('page_key', 'retur-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Retur Pembelian > Tambah')

@section('content')
    @php
        $penerimaanOptions = $penerimaans->map(function ($penerimaan) {
            $pesananDetails = $penerimaan->pesanan?->details ?? collect();

            $returnedDetails = $penerimaan->returPembelian
                ->flatMap(fn ($retur) => $retur->details);

            return [
                'value' => $penerimaan->no_penerimaan,
                'label' => $penerimaan->no_penerimaan
                    . ' — '
                    . ($penerimaan->pesanan?->no_order_pembelian ?? '-')
                    . ' — '
                    . ($penerimaan->pesanan?->vendor?->nama_vendor ?? '-'),
                'vendor' => [
                    'nama' => $penerimaan->pesanan?->vendor?->nama_vendor ?? '',
                ],
                'items' => $penerimaan->details
                    ->map(function ($detail) use ($pesananDetails, $returnedDetails) {
                        $sudahDiretur = $returnedDetails
                            ->where('sku', $detail->sku)
                            ->sum('jumlah');

                        $sisaRetur = max(
                            (int) $detail->kuantitas - (int) $sudahDiretur,
                            0
                        );

                        $pesananDetail = $pesananDetails->firstWhere(
                            'sku',
                            $detail->sku
                        );

                        $kuantitasPesanan = max(
                            (int) ($pesananDetail?->kuantitas ?? 1),
                            1
                        );

                        $diskonPerUnit = (float) ($pesananDetail?->diskon ?? 0)
                            / $kuantitasPesanan;

                        return [
                            'sku' => $detail->sku,
                            'label' => $detail->sku
                                . ' — '
                                . ($detail->barang?->nama_barang ?? 'Barang tidak ditemukan'),
                            'kuantitas' => $sisaRetur,
                            'harga_unit' => (float) ($pesananDetail?->harga_unit ?? 0),
                            'diskon_per_unit' => $diskonPerUnit,
                        ];
                    })
                    ->filter(fn ($item) => $item['kuantitas'] > 0)
                    ->values()
                    ->all(),
            ];
        })->values()->all();

        $details = old('details', [[
            'sku' => '',
            'jumlah' => 1,
            'harga_unit' => 0,
            'diskon' => 0,
        ]]);

        if (empty($details)) {
            $details = [[
                'sku' => '',
                'jumlah' => 1,
                'harga_unit' => 0,
                'diskon' => 0,
            ]];
        }

        $nextDetailIndex = max(
            array_map('intval', array_keys($details))
        ) + 1;
    @endphp

    <style>
        .retur-pembelian-form-page .form-section {
            margin-bottom: 24px;
        }

        .retur-pembelian-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .retur-pembelian-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .retur-pembelian-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .retur-pembelian-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .retur-pembelian-form-page .required {
            color: #dc3545;
        }

        .retur-pembelian-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .retur-pembelian-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .retur-pembelian-form-page .section-description {
            margin: 0 0 18px;
            color: #73808c;
            font-size: 13px;
        }

        .retur-pembelian-form-page .form-help {
            margin-top: 8px;
            color: #73808c;
            font-size: 12px;
        }

        .retur-pembelian-form-page .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 16px;
        }

        .retur-pembelian-form-page .retur-detail-table {
            width: 100%;
            min-width: 1020px;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .retur-pembelian-form-page .retur-detail-table th,
        .retur-pembelian-form-page .retur-detail-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .retur-pembelian-form-page .retur-detail-table th {
            text-align: center;
            white-space: normal;
        }

        .retur-pembelian-form-page .retur-detail-table td:nth-child(2),
        .retur-pembelian-form-page .retur-detail-table td:last-child {
            text-align: center;
        }

        .retur-pembelian-form-page .retur-detail-table td:last-child .btn {
            white-space: nowrap;
        }

        .retur-pembelian-form-page .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 767px) {
            .retur-pembelian-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="retur-pembelian-form-page">
        <x-gentelella::page-header
            title="Tambah Retur Pembelian"
            pretitle="Purchasing Cycle"
        />

        <x-gentelella::card title="Informasi Retur Pembelian">
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

            @if ($penerimaans->isEmpty())
                <div class="banner banner-danger" role="alert">
                    Belum ada Penerimaan Barang. Catat penerimaan terlebih dahulu
                    sebelum membuat retur pembelian.
                </div>

                <a
                    href="{{ route('penerimaan-barang.index') }}"
                    class="btn btn-outline"
                >
                    Buka Penerimaan Barang
                </a>
            @else
                <form action="{{ route('retur-pembelian.store') }}" method="POST">
                    @csrf

                    <div class="form-section">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="no_retur" class="form-label">
                                    No. Retur <span class="required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="no_retur"
                                    name="no_retur"
                                    value="{{ old('no_retur', $nextNoRetur) }}"
                                    class="form-control"
                                    maxlength="255"
                                    placeholder="Contoh: RT-2026-001"
                                    required
                                >
                                @error('no_retur')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tanggal" class="form-label">
                                    Tanggal Retur <span class="required">*</span>
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
                                            @disabled(count($penerimaan['items']) === 0)
                                        >
                                            {{ $penerimaan['label'] }}{{ count($penerimaan['items']) === 0 ? ' — Tidak ada sisa retur' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('no_penerimaan')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-help">
                                    Pilih penerimaan barang yang menjadi dasar retur.
                                </div>
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
                                <label for="alamat_pembeli" class="form-label">
                                    Alamat Pembeli <span class="required">*</span>
                                </label>
                                <textarea
                                    id="alamat_pembeli"
                                    name="alamat_pembeli"
                                    class="form-control"
                                    rows="3"
                                    required
                                >{{ old('alamat_pembeli') }}</textarea>
                                @error('alamat_pembeli')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nama_penjual" class="form-label">
                                    Nama Penjual <span class="required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_penjual"
                                    name="nama_penjual"
                                    value="{{ old('nama_penjual') }}"
                                    class="form-control"
                                    maxlength="255"
                                    required
                                >
                                @error('nama_penjual')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="alamat_penjual" class="form-label">
                                    Alamat Penjual <span class="required">*</span>
                                </label>
                                <textarea
                                    id="alamat_penjual"
                                    name="alamat_penjual"
                                    class="form-control"
                                    rows="3"
                                    required
                                >{{ old('alamat_penjual') }}</textarea>
                                @error('alamat_penjual')
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
                        </div>
                    </div>

                    <hr>

                    <div class="form-section">
                        <h3 class="section-heading">Detail Barang Retur</h3>
                        <p id="receipt-items-help" class="section-description">
                            Pilih penerimaan barang untuk memuat daftar barang yang masih bisa diretur.
                        </p>

                        @foreach ($details as $index => $detail)
                            @error("details.$index.sku")
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            @error("details.$index.jumlah")
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
                            <table class="table table-striped retur-detail-table">
                                <colgroup>
                                    <col style="width: 27%">
                                    <col style="width: 14%">
                                    <col style="width: 13%">
                                    <col style="width: 16%">
                                    <col style="width: 15%">
                                    <col style="width: 15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Barang / SKU</th>
                                        <th>Sisa Bisa Diretur</th>
                                        <th>Jumlah Retur</th>
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
                                                <span data-available-label>-</span>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    name="details[{{ $index }}][jumlah]"
                                                    class="form-control"
                                                    min="1"
                                                    step="1"
                                                    value="{{ $detail['jumlah'] ?? 1 }}"
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
                                                    data-user-edited="{{ array_key_exists('harga_unit', $detail) ? 'true' : '' }}"
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
                            href="{{ route('retur-pembelian.index') }}"
                            class="btn btn-outline"
                        >
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Retur
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
                            <span data-available-label>-</span>
                        </td>
                        <td>
                            <input
                                type="number"
                                name="details[__INDEX__][jumlah]"
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
                                Hapus
                            </button>
                        </td>
                    </tr>
                </template>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const receiptSelect = document.getElementById('no_penerimaan');
                        const namaPenjualInput = document.getElementById('nama_penjual');
                        const detailRows = document.getElementById('detail-rows');
                        const template = document.getElementById('detail-row-template');
                        const addButton = document.getElementById('add-detail');
                        const helpText = document.getElementById('receipt-items-help');

                        const receiptOptions = {{ \Illuminate\Support\Js::from($penerimaanOptions) }};
                        let nextIndex = {{ $nextDetailIndex }};

                        function refreshSkuOptions() {
                            const selectedReceipt = receiptOptions.find(
                                receipt => String(receipt.value) === receiptSelect.value
                            );
                            if (namaPenjualInput && selectedReceipt) {
                                namaPenjualInput.value = selectedReceipt.vendor?.nama ?? '';
                            }

                            const items = selectedReceipt ? selectedReceipt.items : [];

                            detailRows.querySelectorAll('[data-detail-row]').forEach(row => {
                                const select = row.querySelector('[data-sku-select]');
                                const quantity = row.querySelector('[data-quantity-input]');
                                const price = row.querySelector('[data-price-input]');
                                const availableLabel = row.querySelector('[data-available-label]');
                                const previousSku = select.value || select.dataset.selectedSku || '';

                                select.replaceChildren(new Option('Pilih barang', ''));

                                items.forEach(item => {
                                    const option = document.createElement('option');
                                    option.textContent = item.label;
                                    option.value = String(item.sku);
                                    option.dataset.availableQuantity = String(item.kuantitas);
                                    option.dataset.unitPrice = String(item.harga_unit);
                                    option.dataset.discountPerUnit = String(item.diskon_per_unit ?? 0);

                                    select.appendChild(option);
                                });

                                select.value = items.some(
                                    item => String(item.sku) === String(previousSku)
                                ) ? String(previousSku) : '';

                                select.dataset.selectedSku = '';
                                select.disabled = items.length === 0;

                                const selectedOption = select.selectedOptions[0];

                                if (selectedOption && selectedOption.value) {
                                    quantity.max = selectedOption.dataset.availableQuantity;
                                    availableLabel.textContent = selectedOption.dataset.availableQuantity;

                                    if (price.dataset.userEdited !== 'true') {
                                        price.value = selectedOption.dataset.unitPrice;
                                    }
                                } else {
                                    quantity.removeAttribute('max');
                                    availableLabel.textContent = '-';
                                }
                            });

                            helpText.textContent = !receiptSelect.value
                                ? 'Pilih penerimaan barang untuk memuat daftar barang yang masih bisa diretur.'
                                : items.length === 0
                                    ? 'Tidak ada barang yang tersisa untuk diretur pada penerimaan ini.'
                                    : 'Jumlah retur tidak boleh melebihi sisa kuantitas yang tersedia.';
                        }

                        detailRows.addEventListener('change', function (event) {
                            if (!event.target.matches('[data-sku-select]')) {
                                return;
                            }

                            const row = event.target.closest('[data-detail-row]');
                            const option = event.target.selectedOptions[0];
                            const quantity = row.querySelector('[data-quantity-input]');
                            const price = row.querySelector('[data-price-input]');
                            const availableLabel = row.querySelector('[data-available-label]');

                            if (option && option.value) {
                                quantity.max = option.dataset.availableQuantity;
                                quantity.value = option.dataset.availableQuantity;
                                availableLabel.textContent = option.dataset.availableQuantity;

                                price.value = option.dataset.unitPrice;
                                price.dataset.userEdited = '';
                                const discount = row.querySelector('input[name$="[diskon]"]');

                                if (discount) {
                                    const diskonPerUnit = Number(option.dataset.discountPerUnit || 0);
                                    discount.value = (diskonPerUnit * Number(quantity.value)).toFixed(2);
                                    discount.dataset.userEdited = '';
                                }
                            } else {
                                quantity.removeAttribute('max');
                                availableLabel.textContent = '-';
                            }
                        });

                        detailRows.addEventListener('input', function (event) {
                            if (event.target.matches('[data-price-input]')) {
                                event.target.dataset.userEdited = 'true';
                            }

                            if (event.target.matches('[data-quantity-input]')) {
                                const row = event.target.closest('[data-detail-row]');
                                const option = row.querySelector('[data-sku-select]')?.selectedOptions[0];
                                const discount = row.querySelector('input[name$="[diskon]"]');

                                if (option?.value && discount?.dataset.userEdited !== 'true') {
                                    const diskonPerUnit = Number(option.dataset.discountPerUnit || 0);

                                    discount.value = (
                                        diskonPerUnit * Number(event.target.value || 0)
                                    ).toFixed(2);
                                }
                            }

                            if (event.target.matches('input[name$="[diskon]"]')) {
                                event.target.dataset.userEdited = 'true';
                            }
                        });

                        addButton.addEventListener('click', function () {
                            const html = template.innerHTML.replace(
                                /__INDEX__/g,
                                String(nextIndex++)
                            );

                            detailRows.insertAdjacentHTML('beforeend', html);
                            refreshSkuOptions();
                        });

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
                                row.querySelector('[data-quantity-input]').value = 1;
                                row.querySelector('[data-price-input]').value = 0;
                                row.querySelector('[data-price-input]').dataset.userEdited = '';
                                row.querySelector('[data-discount]').value = 0;
                                row.querySelector('[data-available-label]').textContent = '-';
                            }

                            refreshSkuOptions();
                        });

                        receiptSelect.addEventListener('change', function () {
                            const selectedReceipt = receiptOptions.find(
                                receipt => String(receipt.value) === receiptSelect.value
                            );

                            if (namaPenjualInput) {
                                namaPenjualInput.value = selectedReceipt?.vendor?.nama ?? '';
                            }

                            refreshSkuOptions();
                        });

                        refreshSkuOptions();
                    });
                </script>
            @endif
        </x-gentelella::card>
    </div>
@endsection
