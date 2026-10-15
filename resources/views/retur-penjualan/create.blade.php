
@extends('gentelella::page')

@section('title', 'Tambah Retur Penjualan')
@section('page_key', 'retur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Retur Penjualan > Tambah')

@section('content')
    @php
        $shipmentOptions = $pengirimans->map(function ($pengiriman) {
            $pesanan = $pengiriman->pesananPenjualan;

            $prices = ($pesanan?->details ?? collect())->keyBy('sku');

            $returned = ($pengiriman->returPenjualan ?? collect())
                ->flatMap(fn ($retur) => $retur->details)
                ->groupBy('sku')
                ->map(fn ($rows) => (int) $rows->sum('jumlah'));

            $items = $pengiriman->details
                ->groupBy('sku')
                ->map(function ($rows, $sku) use ($prices, $returned) {
                    $barang = $rows->first()->barang;
                    $dikirim = (int) $rows->sum('total');
                    $sudahDiretur = (int) $returned->get($sku, 0);
                    $harga = (float) ($prices->get($sku)?->harga_unit ?? 0);

                    return [
                        'sku' => $sku,
                        'nama' => $barang?->nama_barang ?? $sku,
                        'dikirim' => $dikirim,
                        'tersedia' => max($dikirim - $sudahDiretur, 0),
                        'harga' => $harga,
                    ];
                })
                ->values();

            return [
                'id' => $pengiriman->id_pengiriman_pesanan,
                'label' => 'Pengiriman #' .
                    $pengiriman->id_pengiriman_pesanan .
                    ' — ' .
                    ($pesanan?->no_pesanan ?? '-'),
                'no_pesanan' => $pesanan?->no_pesanan ?? '',
                'nama_pembeli' => $pesanan?->nama_pembeli ?? '',
                'alamat_pembeli' => $pesanan?->alamat ?? '',
                'items' => $items,
            ];
        })->values();
    @endphp

    <style>
        .retur-penjualan-form-page .form-section {
            margin-bottom: 24px;
        }

        .retur-penjualan-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .retur-penjualan-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .retur-penjualan-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .retur-penjualan-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .retur-penjualan-form-page .required {
            color: #dc3545;
        }

        .retur-penjualan-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .retur-penjualan-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .retur-penjualan-form-page .section-description {
            margin: 0 0 18px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .retur-penjualan-form-page .form-help {
            display: block;
            margin-top: 8px;
            color: #73808c;
            font-size: 12px;
        }

        .retur-penjualan-form-page .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 16px;
        }

        .retur-penjualan-form-page .retur-detail-table {
            width: 100%;
            min-width: 1100px;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .retur-penjualan-form-page .retur-detail-table th,
        .retur-penjualan-form-page .retur-detail-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .retur-penjualan-form-page .retur-detail-table th {
            text-align: center;
            white-space: normal;
        }

        .retur-penjualan-form-page .retur-detail-table td:nth-child(2),
        .retur-penjualan-form-page .retur-detail-table td:nth-child(6),
        .retur-penjualan-form-page .retur-detail-table td:last-child {
            text-align: center;
        }

        .retur-penjualan-form-page .retur-detail-table td:last-child .btn {
            white-space: nowrap;
        }

        .retur-penjualan-form-page .total-summary {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #344256;
            border-radius: 6px;
            background: #1c293a;
            color: #f1f5f9;
        }

        .retur-penjualan-form-page .total-summary h4 {
            margin: 0 0 18px;
            color: #f1f5f9;
            font-size: 16px;
            font-weight: 600;
        }

        .retur-penjualan-form-page .total-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .retur-penjualan-form-page .total-label {
            margin-bottom: 6px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .retur-penjualan-form-page .total-value {
            overflow-wrap: anywhere;
            color: #ffffff;
            font-size: 17px;
            font-weight: 600;
        }

        .retur-penjualan-form-page .total-help {
            margin: 16px 0 0;
            color: #cbd5e1;
            font-size: 12px;
        }

        .retur-penjualan-form-page .form-actions {
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
            .retur-penjualan-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .retur-penjualan-form-page .total-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 14px;
            }
        }
    </style>

    <div class="retur-penjualan-form-page">
        <x-gentelella::page-header
            title="Tambah Retur Penjualan"
            pretitle="Sales Cycle"
        />

        <x-gentelella::card title="Informasi Retur Penjualan">
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

            <form
                action="{{ route('retur-penjualan.store') }}"
                method="POST"
                id="retur-form"
            >
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
                                class="form-control"
                                value="{{ old('no_retur') }}"
                                placeholder="Contoh: RTJ-2026-001"
                                maxlength="255"
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
                                class="form-control"
                                value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                                required
                            >
                            @error('tanggal')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_pengiriman_pesanan" class="form-label">
                                Pengiriman Penjualan <span class="required">*</span>
                            </label>
                            <select
                                id="id_pengiriman_pesanan"
                                name="id_pengiriman_pesanan"
                                class="form-control"
                                required
                            >
                                <option value="">Pilih pengiriman</option>
                                @foreach ($shipmentOptions as $shipment)
                                    <option
                                        value="{{ $shipment['id'] }}"
                                        @selected((string) old('id_pengiriman_pesanan') === (string) $shipment['id'])
                                    >
                                        {{ $shipment['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pengiriman_pesanan')
                                <div class="form-error">{{ $message }}</div>
                            @enderror

                            @if ($shipmentOptions->isEmpty())
                                <span class="form-help">
                                    Belum ada pengiriman penjualan. Buat pengiriman terlebih dahulu.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Informasi Pembeli</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_pembeli" class="form-label">
                                Nama Pembeli <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama_pembeli"
                                name="nama_pembeli"
                                class="form-control"
                                value="{{ old('nama_pembeli') }}"
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
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Informasi Penjual</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_penjual" class="form-label">
                                Nama Penjual <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama_penjual"
                                name="nama_penjual"
                                class="form-control"
                                value="{{ old('nama_penjual') }}"
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
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Detail Barang yang Diretur</h3>
                    <p class="section-description">
                        Pilih barang dari pengiriman. Jumlah retur tidak boleh melebihi
                        sisa barang yang belum pernah diretur.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-striped retur-detail-table" id="detail-table">
                            <colgroup>
                                <col style="width: 24%">
                                <col style="width: 12%">
                                <col style="width: 13%">
                                <col style="width: 14%">
                                <col style="width: 12%">
                                <col style="width: 14%">
                                <col style="width: 11%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah Tersedia</th>
                                    <th>Jumlah Retur</th>
                                    <th>Harga Unit</th>
                                    <th>Diskon</th>
                                    <th>Total Baris</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detail-body">
                                <tr>
                                    <td colspan="7" class="muted">
                                        Pilih pengiriman untuk memuat barang.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button
                        type="button"
                        id="add-detail"
                        class="btn btn-outline"
                        disabled
                    >
                        + Tambah Baris Barang
                    </button>
                </div>

                <div class="form-section">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="ppn" class="form-label">
                                PPN (nominal rupiah) <span class="required">*</span>
                            </label>
                            <input
                                type="number"
                                id="ppn"
                                name="ppn"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="{{ old('ppn', '0') }}"
                                required
                            >
                            @error('ppn')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="total-summary">
                        <h4>Ringkasan Retur</h4>

                        <div class="total-grid">
                            <div>
                                <div class="total-label">Subtotal setelah diskon</div>
                                <div class="total-value" id="subtotal-display">Rp0,00</div>
                            </div>
                            <div>
                                <div class="total-label">PPN</div>
                                <div class="total-value" id="ppn-display">Rp0,00</div>
                            </div>
                            <div>
                                <div class="total-label">Total Retur</div>
                                <div class="total-value" id="total-display">Rp0,00</div>
                            </div>
                        </div>

                        <p class="total-help">
                            Total dihitung ulang oleh server saat data disimpan.
                        </p>
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('retur-penjualan.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Retur
                    </button>
                </div>
            </form>
        </x-gentelella::card>
    </div>

    <template id="detail-row-template">
        <tr data-detail-row>
            <td>
                <select
                    name="details[__INDEX__][sku]"
                    class="form-control"
                    data-sku
                    required
                >
                    <option value="">Pilih barang</option>
                </select>
            </td>
            <td data-available>0</td>
            <td>
                <input
                    type="number"
                    name="details[__INDEX__][jumlah]"
                    class="form-control"
                    data-quantity
                    min="1"
                    step="1"
                    required
                >
            </td>
            <td>
                <input
                    type="number"
                    name="details[__INDEX__][harga_unit]"
                    class="form-control"
                    data-price
                    min="0"
                    step="0.01"
                    required
                >
            </td>
            <td>
                <input
                    type="number"
                    name="details[__INDEX__][diskon]"
                    class="form-control"
                    data-discount
                    min="0"
                    step="0.01"
                    value="0"
                >
            </td>
            <td data-line-total>Rp0,00</td>
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
        const shipmentData = @json($shipmentOptions);
        const oldDetails = @json(old('details', []));
        const oldShipmentId = @json(old('id_pengiriman_pesanan'));

        const shipmentSelect = document.getElementById('id_pengiriman_pesanan');
        const detailBody = document.getElementById('detail-body');
        const addDetailButton = document.getElementById('add-detail');
        const rowTemplate = document.getElementById('detail-row-template');

        const buyerName = document.getElementById('nama_pembeli');
        const buyerAddress = document.getElementById('alamat_pembeli');
        const ppnInput = document.getElementById('ppn');

        let rowIndex = 0;

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value || 0);
        }

        function currentShipment() {
            return shipmentData.find(
                item => String(item.id) === String(shipmentSelect.value)
            );
        }

        function updateRow(row) {
            const shipment = currentShipment();
            const skuSelect = row.querySelector('[data-sku]');
            const quantityInput = row.querySelector('[data-quantity]');
            const priceInput = row.querySelector('[data-price]');
            const discountInput = row.querySelector('[data-discount]');

            const selectedItem = shipment?.items.find(
                item => item.sku === skuSelect.value
            );

            if (!selectedItem) {
                row.querySelector('[data-available]').textContent = '0';
                quantityInput.removeAttribute('max');
                row.querySelector('[data-line-total]').textContent = formatRupiah(0);
                updateSummary();
                return;
            }

            row.querySelector('[data-available]').textContent =
                selectedItem.tersedia;

            quantityInput.max = selectedItem.tersedia;

            if (Number(quantityInput.value) > selectedItem.tersedia) {
                quantityInput.value = selectedItem.tersedia;
            }

            priceInput.value = selectedItem.harga;

            const quantity = Number(quantityInput.value) || 0;
            const price = Number(priceInput.value) || 0;
            const discount = Number(discountInput.value) || 0;
            const lineTotal = Math.max(quantity * price - discount, 0);

            row.querySelector('[data-line-total]').textContent =
                formatRupiah(lineTotal);

            updateSummary();
        }

        function addDetailRow(detail = {}) {
            const shipment = currentShipment();

            if (!shipment) {
                return;
            }

            const fragment = rowTemplate.content.cloneNode(true);
            const row = fragment.querySelector('[data-detail-row]');

            row.innerHTML = row.innerHTML.replaceAll(
                '__INDEX__',
                String(rowIndex++)
            );

            const skuSelect = row.querySelector('[data-sku]');
            const quantityInput = row.querySelector('[data-quantity]');
            const priceInput = row.querySelector('[data-price]');
            const discountInput = row.querySelector('[data-discount]');

            shipment.items
                .filter(item => item.tersedia > 0)
                .forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.sku;
                    option.textContent =
                        `${item.sku} — ${item.nama} (tersedia: ${item.tersedia})`;
                    skuSelect.appendChild(option);
                });

            if (detail.sku) {
                skuSelect.value = detail.sku;
            }

            quantityInput.value = detail.jumlah ?? '';
            priceInput.value = detail.harga_unit ?? '';
            discountInput.value = detail.diskon ?? 0;

            skuSelect.addEventListener('change', () => {
                const item = shipment.items.find(
                    entry => entry.sku === skuSelect.value
                );

                if (item) {
                    priceInput.value = item.harga;
                    quantityInput.value = item.tersedia > 0 ? 1 : 0;
                }

                updateRow(row);
            });

            [quantityInput, priceInput, discountInput].forEach(input => {
                input.addEventListener('input', () => updateRow(row));
            });

            row.querySelector('[data-remove-row]').addEventListener(
                'click',
                () => {
                    row.remove();

                    if (!detailBody.querySelector('[data-detail-row]')) {
                        addDetailRow();
                    }

                    updateSummary();
                }
            );

            detailBody.appendChild(row);

            if (skuSelect.value) {
                updateRow(row);
            }
        }

        function loadShipmentDetails() {
            const shipment = currentShipment();

            detailBody.innerHTML = '';
            rowIndex = 0;

            if (!shipment) {
                detailBody.innerHTML =
                    '<tr><td colspan="7" class="muted">Pilih pengiriman untuk memuat barang.</td></tr>';

                addDetailButton.disabled = true;
                buyerName.value = '';
                buyerAddress.value = '';
                updateSummary();
                return;
            }

            buyerName.value = shipment.nama_pembeli;
            buyerAddress.value = shipment.alamat_pembeli;

            const availableItems = shipment.items.filter(
                item => item.tersedia > 0
            );

            if (availableItems.length === 0) {
                detailBody.innerHTML =
                    '<tr><td colspan="7" class="muted">Semua barang pada pengiriman ini sudah diretur.</td></tr>';

                addDetailButton.disabled = true;
                updateSummary();
                return;
            }

            addDetailButton.disabled = false;

            if (
                Array.isArray(oldDetails) &&
                oldDetails.length > 0 &&
                String(oldShipmentId) === String(shipment.id)
            ) {
                oldDetails.forEach(detail => addDetailRow(detail));
            } else {
                addDetailRow();
            }

            updateSummary();
        }

        function updateSummary() {
            let subtotal = 0;

            detailBody.querySelectorAll('[data-detail-row]').forEach(row => {
                const quantity =
                    Number(row.querySelector('[data-quantity]').value) || 0;
                const price =
                    Number(row.querySelector('[data-price]').value) || 0;
                const discount =
                    Number(row.querySelector('[data-discount]').value) || 0;

                subtotal += Math.max(quantity * price - discount, 0);
            });

            const ppn = Number(ppnInput.value) || 0;

            document.getElementById('subtotal-display').textContent =
                formatRupiah(subtotal);

            document.getElementById('ppn-display').textContent =
                formatRupiah(ppn);

            document.getElementById('total-display').textContent =
                formatRupiah(subtotal + ppn);
        }

        shipmentSelect.addEventListener('change', loadShipmentDetails);

        addDetailButton.addEventListener('click', () => addDetailRow());

        ppnInput.addEventListener('input', updateSummary);

        document.getElementById('retur-form').addEventListener('submit', event => {
            const rows = detailBody.querySelectorAll('[data-detail-row]');

            if (rows.length === 0) {
                event.preventDefault();
                alert('Tambahkan setidaknya satu barang retur.');
                return;
            }

            for (const row of rows) {
                const qty = Number(row.querySelector('[data-quantity]').value);
                const max = Number(row.querySelector('[data-quantity]').max);
                const price = Number(row.querySelector('[data-price]').value);
                const discount = Number(row.querySelector('[data-discount]').value);

                if (
                    !row.querySelector('[data-sku]').value ||
                    qty < 1 ||
                    qty > max ||
                    price < 0 ||
                    discount < 0 ||
                    discount > qty * price
                ) {
                    event.preventDefault();
                    alert('Periksa kembali barang, jumlah, harga, dan diskon retur.');
                    return;
                }
            }
        });

        loadShipmentDetails();
        updateSummary();
    </script>
@endsection
