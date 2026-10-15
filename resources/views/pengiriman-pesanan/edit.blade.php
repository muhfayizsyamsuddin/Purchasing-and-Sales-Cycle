
@extends('gentelella::page')

@section('title', 'Edit Pengiriman Pesanan')
@section('page_key', 'pengiriman-pesanan')
@section('breadcrumb', 'Home > Sales Cycle > Pengiriman Pesanan > Edit')

@section('content')
    @php
        $defaultDetails = $pengiriman->details->map(function ($detail) {
            return [
                'sku' => $detail->sku,
                'keterangan' => $detail->keterangan,
                'total' => $detail->total,
            ];
        })->all();

        $oldDetails = old('details', $defaultDetails);

        if (empty($oldDetails)) {
            $oldDetails = [
                ['sku' => '', 'keterangan' => '', 'total' => 1],
            ];
        }

        $nextIndex = max(array_map('intval', array_keys($oldDetails))) + 1;

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

    <style>
        .pengiriman-pesanan-form-page .form-section {
            margin-bottom: 24px;
        }

        .pengiriman-pesanan-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .pengiriman-pesanan-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .pengiriman-pesanan-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .pengiriman-pesanan-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .pengiriman-pesanan-form-page .required {
            color: #dc3545;
        }

        .pengiriman-pesanan-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .pengiriman-pesanan-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .pengiriman-pesanan-form-page .section-description {
            margin: 0 0 18px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .pengiriman-pesanan-form-page .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 16px;
        }

        .pengiriman-pesanan-form-page .detail-table {
            width: 100%;
            min-width: 900px;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .pengiriman-pesanan-form-page .detail-table th,
        .pengiriman-pesanan-form-page .detail-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .pengiriman-pesanan-form-page .detail-table th {
            text-align: center;
            white-space: normal;
        }

        .pengiriman-pesanan-form-page .detail-table td:last-child {
            text-align: center;
        }

        .pengiriman-pesanan-form-page .detail-table td:last-child .btn {
            white-space: nowrap;
        }

        .pengiriman-pesanan-form-page .form-actions {
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
            .pengiriman-pesanan-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="pengiriman-pesanan-form-page">
        <x-gentelella::page-header
            title="Edit Pengiriman Pesanan"
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

            @if (session('error'))
                <div class="banner banner-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form
                action="{{ route('pengiriman-pesanan.update', $pengiriman->id_pengiriman_pesanan) }}"
                method="POST"
                id="pengiriman-form"
            >
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="form-grid">
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
                                        @selected(old('no_pesanan', $pengiriman->no_pesanan) == $pesanan->no_pesanan)
                                    >
                                        {{ $pesanan->no_pesanan }} — {{ $pesanan->nama_pembeli }}
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
                                value="{{ old('tanggal', $pengiriman->tanggal->format('Y-m-d')) }}"
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
                                value="{{ old('nama_pengirim', $pengiriman->nama_pengirim) }}"
                                maxlength="255"
                                required
                            >

                            @error('nama_pengirim')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Detail Pengiriman</h3>
                    <p class="section-description">
                        Periksa barang dan jumlah yang dikirim. Pilihan SKU mengikuti pesanan penjualan yang dipilih.
                    </p>

                    @foreach ($oldDetails as $index => $detail)
                        @error("details.$index.sku")
                            <div class="form-error">{{ $message }}</div>
                        @enderror

                        @error("details.$index.keterangan")
                            <div class="form-error">{{ $message }}</div>
                        @enderror

                        @error("details.$index.total")
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    @endforeach

                    <div class="table-responsive">
                        <table class="table table-striped detail-table">
                            <colgroup>
                                <col style="width: 35%">
                                <col style="width: 30%">
                                <col style="width: 20%">
                                <col style="width: 15%">
                            </colgroup>

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
                        href="{{ route('pengiriman-pesanan.index') }}"
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
                            Hapus
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
                            select.add(new Option(
                                item.sku + ' — ' + item.nama_barang,
                                item.sku
                            ));
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

                        populateSKUSelect(
                            newRow.querySelector('[data-sku-select]')
                        );

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
        </x-gentelella::card>
    </div>
@endsection
