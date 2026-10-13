
@extends('gentelella::page')

@section('title', 'Edit Retur Penjualan')
@section('page_key', 'retur-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Retur Penjualan > Edit')

@section('content')
    @php
        $shipmentOptions = $pengirimans->map(function ($pengiriman) use ($retur) {
            $pesanan = $pengiriman->pesananPenjualan;

            $prices = ($pesanan?->details ?? collect())->keyBy('sku');

            $returned = ($pengiriman->returPenjualan ?? collect())
                ->reject(fn ($item) => $item->no_retur === $retur->no_retur)
                ->flatMap(fn ($item) => $item->details)
                ->groupBy('sku')
                ->map(fn ($rows) => (int) $rows->sum('jumlah'));

            $items = $pengiriman->details
                ->groupBy('sku')
                ->map(function ($rows, $sku) use ($prices, $returned) {
                    $barang = $rows->first()->barang;
                    $dikirim = (int) $rows->sum('total');
                    $sudahDiretur = (int) $returned->get($sku, 0);

                    return [
                        'sku' => $sku,
                        'nama' => $barang?->nama_barang ?? $sku,
                        'dikirim' => $dikirim,
                        'tersedia' => max($dikirim - $sudahDiretur, 0),
                        'harga' => (float) ($prices->get($sku)?->harga_unit ?? 0),
                    ];
                })
                ->values();

            return [
                'id' => $pengiriman->id_pengiriman_pesanan,
                'label' => 'Pengiriman #' .
                    $pengiriman->id_pengiriman_pesanan .
                    ' — ' .
                    ($pesanan?->no_pesanan ?? '-'),
                'nama_pembeli' => $pesanan?->nama_pembeli ?? '',
                'alamat_pembeli' => $pesanan?->alamat ?? '',
                'items' => $items,
            ];
        })->values();

        $initialDetails = $retur->details->map(fn ($detail) => [
            'sku' => $detail->sku,
            'jumlah' => $detail->jumlah,
            'harga_unit' => $detail->harga_unit,
            'diskon' => $detail->diskon,
        ])->values()->all();
    @endphp

    <x-gentelella::page-header
        title="Edit Retur Penjualan"
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
            action="{{ route('retur-penjualan.update', $retur->no_retur) }}"
            method="POST"
            id="retur-form"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_retur" class="form-label">
                    No. Retur <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="no_retur"
                    name="no_retur"
                    class="form-control"
                    value="{{ old('no_retur', $retur->no_retur) }}"
                    maxlength="255"
                    required
                >
                @error('no_retur')
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
                            @selected((string) old('id_pengiriman_pesanan', $retur->id_pengiriman_pesanan) === (string) $shipment['id'])
                        >
                            {{ $shipment['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('id_pengiriman_pesanan')
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
                    value="{{ old('tanggal', $retur->tanggal->format('Y-m-d')) }}"
                    required
                >
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h4>Informasi Pembeli</h4>

            <div class="form-group">
                <label for="nama_pembeli" class="form-label">
                    Nama Pembeli <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="nama_pembeli"
                    name="nama_pembeli"
                    class="form-control"
                    value="{{ old('nama_pembeli', $retur->nama_pembeli) }}"
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
                >{{ old('alamat_pembeli', $retur->alamat_pembeli) }}</textarea>
                @error('alamat_pembeli')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h4>Informasi Penjual</h4>

            <div class="form-group">
                <label for="nama_penjual" class="form-label">
                    Nama Penjual <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="nama_penjual"
                    name="nama_penjual"
                    class="form-control"
                    value="{{ old('nama_penjual', $retur->nama_penjual) }}"
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
                >{{ old('alamat_penjual', $retur->alamat_penjual) }}</textarea>
                @error('alamat_penjual')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h4>Detail Barang yang Diretur</h4>

            <p class="muted">
                Pastikan jumlah retur tidak melebihi sisa barang yang belum diretur.
            </p>

            <div style="overflow-x: auto">
                <table class="table" id="detail-table">
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
                    <tbody id="detail-body"></tbody>
                </table>
            </div>

            <button
                type="button"
                id="add-detail"
                class="btn btn-outline"
            >
                Tambah Baris Barang
            </button>

            <div class="form-group" style="margin-top: 20px">
                <label for="PPN" class="form-label">
                    PPN (nominal rupiah) <span class="required">*</span>
                </label>
                <input
                    type="number"
                    id="PPN"
                    name="PPN"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="{{ old('PPN', $retur->PPN) }}"
                    required
                >
                @error('PPN')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 16px; padding: 14px; border: 1px solid #ddd; border-radius: 4px">
                <p>Subtotal setelah diskon:
                    <strong id="subtotal-display">Rp0,00</strong>
                </p>
                <p>PPN:
                    <strong id="ppn-display">Rp0,00</strong>
                </p>
                <p>Total retur:
                    <strong id="total-display">Rp0,00</strong>
                </p>
            </div>

            <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('retur-penjualan.index') }}"
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
        const oldShipmentId = @json(old('id_pengiriman_pesanan', $retur->id_pengiriman_pesanan));
        const oldDetails = @json(old('details', $initialDetails));

        const shipmentSelect = document.getElementById('id_pengiriman_pesanan');
        const detailBody = document.getElementById('detail-body');
        const addDetailButton = document.getElementById('add-detail');
        const rowTemplate = document.getElementById('detail-row-template');
        const buyerName = document.getElementById('nama_pembeli');
        const buyerAddress = document.getElementById('alamat_pembeli');
        const ppnInput = document.getElementById('PPN');

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
            updateRow(row);
        }

        function loadShipmentDetails() {
            const shipment = currentShipment();

            detailBody.innerHTML = '';
            rowIndex = 0;

            if (!shipment) {
                detailBody.innerHTML =
                    '<tr><td colspan="7" class="muted">Pilih pengiriman untuk memuat barang.</td></tr>';

                addDetailButton.disabled = true;
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
                    '<tr><td colspan="7" class="muted">Tidak ada barang yang tersedia untuk diretur.</td></tr>';

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
                const sku = row.querySelector('[data-sku]').value;
                const qty = Number(row.querySelector('[data-quantity]').value);
                const max = Number(row.querySelector('[data-quantity]').max);
                const price = Number(row.querySelector('[data-price]').value);
                const discount = Number(row.querySelector('[data-discount]').value);

                if (
                    !sku ||
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
