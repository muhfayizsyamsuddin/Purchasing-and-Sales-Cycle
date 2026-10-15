
@extends('gentelella::page')

@section('title', 'Buat Pesanan Pembelian')
@section('page_key', 'pesanan-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pesanan Pembelian > Tambah')

@section('content')
    <div class="pesanan-form-page">
        <x-gentelella::page-header
            title="Buat Pesanan Pembelian"
            pretitle="Purchasing Cycle"
        />

        <style>
            .pesanan-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .pesanan-form-grid .form-group {
                min-width: 0;
                margin-bottom: 0;
            }

            .pesanan-form-page .form-control {
                width: 100%;
                box-sizing: border-box;
            }

            .pesanan-detail-section {
                margin-top: 28px;
                padding-top: 24px;
                border-top: 1px solid var(--border-color);
            }

            .pesanan-detail-heading {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 16px;
            }

            .pesanan-detail-heading h3 {
                margin: 0 0 4px;
                font-size: 17px;
                font-weight: 600;
            }

            .pesanan-detail-table {
                width: 100%;
                min-width: 780px;
                table-layout: fixed;
            }

            .pesanan-detail-table th,
            .pesanan-detail-table td {
                padding: 12px;
                vertical-align: middle;
            }

            .pesanan-detail-table th {
                text-align: center;
            }

            .pesanan-detail-table th:nth-child(1) {
                width: 36%;
            }

            .pesanan-detail-table th:nth-child(2) {
                width: 14%;
            }

            .pesanan-detail-table th:nth-child(3) {
                width: 17%;
            }

            .pesanan-detail-table th:nth-child(4) {
                width: 17%;
            }

            .pesanan-detail-table th:nth-child(5) {
                width: 16%;
            }

            .pesanan-detail-table .form-control {
                min-width: 0;
            }

            .pesanan-form-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }

            @media (max-width: 700px) {
                .pesanan-form-grid {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 16px;
                }

                .pesanan-form-actions {
                    flex-wrap: wrap;
                }
            }
        </style>

        <x-gentelella::card title="Informasi Pesanan">
            <p style="color:var(--text-muted); font-size:13px; margin:0 0 24px;">
                Lengkapi informasi pesanan pembelian dan detail barang dari vendor.
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

            <form action="{{ route('pesanan-pembelian.store') }}" method="POST">
                @csrf

                <div class="pesanan-form-grid">
                    <div class="form-group">
                        <label for="no_order_pembelian" class="form-label">
                            No. Order Pembelian <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_order_pembelian"
                            name="no_order_pembelian"
                            value="{{ old('no_order_pembelian') }}"
                            class="form-control @error('no_order_pembelian') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Masukkan nomor order"
                            required
                        >

                        @error('no_order_pembelian')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal" class="form-label">
                            Tanggal <span class="required">*</span>
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
                        <label for="tanggal_dibutuhkan" class="form-label">
                            Tanggal Dibutuhkan <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="tanggal_dibutuhkan"
                            name="tanggal_dibutuhkan"
                            value="{{ old('tanggal_dibutuhkan') }}"
                            class="form-control @error('tanggal_dibutuhkan') is-invalid @enderror"
                            required
                        >

                        @error('tanggal_dibutuhkan')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="id_vendor" class="form-label">
                            Vendor <span class="required">*</span>
                        </label>

                        <select
                            id="id_vendor"
                            name="id_vendor"
                            class="form-control @error('id_vendor') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih vendor</option>

                            @foreach ($vendors as $vendor)
                                <option
                                    value="{{ $vendor->id_vendor }}"
                                    @selected(old('id_vendor') == $vendor->id_vendor)
                                >
                                    {{ $vendor->nama_vendor }}
                                </option>
                            @endforeach
                        </select>

                        @error('id_vendor')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="no_faktur" class="form-label">
                            No. Faktur
                        </label>

                        <input
                            type="text"
                            id="no_faktur"
                            name="no_faktur"
                            value="{{ old('no_faktur') }}"
                            class="form-control @error('no_faktur') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Opsional"
                        >

                        @error('no_faktur')
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
                            class="form-control @error('ppn') is-invalid @enderror"
                            min="0"
                            step="0.01"
                            required
                        >

                        @error('ppn')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="pesanan-detail-section">
                    <div class="pesanan-detail-heading">
                        <div>
                            <h3>Detail Barang</h3>
                            <p style="color:var(--text-muted); font-size:13px; margin:0;">
                                Pilih barang serta isi kuantitas, harga unit, dan diskon.
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
                        <table class="table pesanan-detail-table">
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
                                @foreach (old('details', [['sku' => '', 'kuantitas' => 1, 'harga_unit' => 0, 'diskon' => 0]]) as $index => $detail)
                                    <tr data-detail-row>
                                        <td>
                                            <select
                                                name="details[{{ $index }}][sku]"
                                                class="form-control @error("details.$index.sku") is-invalid @enderror"
                                                required
                                            >
                                                <option value="">Pilih barang</option>

                                                @foreach ($barangs as $barang)
                                                    <option
                                                        value="{{ $barang->sku }}"
                                                        @selected(($detail['sku'] ?? '') == $barang->sku)
                                                    >
                                                        {{ $barang->sku }} — {{ $barang->nama_barang }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error("details.$index.sku")
                                                <div class="form-error">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="details[{{ $index }}][kuantitas]"
                                                class="form-control @error("details.$index.kuantitas") is-invalid @enderror"
                                                min="1"
                                                value="{{ $detail['kuantitas'] ?? 1 }}"
                                                required
                                            >

                                            @error("details.$index.kuantitas")
                                                <div class="form-error">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="details[{{ $index }}][harga_unit]"
                                                class="form-control @error("details.$index.harga_unit") is-invalid @enderror"
                                                min="0"
                                                step="0.01"
                                                value="{{ $detail['harga_unit'] ?? 0 }}"
                                                required
                                            >

                                            @error("details.$index.harga_unit")
                                                <div class="form-error">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="details[{{ $index }}][diskon]"
                                                class="form-control @error("details.$index.diskon") is-invalid @enderror"
                                                min="0"
                                                step="0.01"
                                                value="{{ $detail['diskon'] ?? 0 }}"
                                            >

                                            @error("details.$index.diskon")
                                                <div class="form-error">{{ $message }}</div>
                                            @enderror
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

                <div class="pesanan-form-actions">
                    <a
                        href="{{ route('pesanan-pembelian.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Pesanan
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
                    required
                >
                    <option value="">Pilih barang</option>

                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->sku }}">
                            {{ $barang->sku }} — {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>
                <input
                    type="number"
                    name="details[__INDEX__][kuantitas]"
                    class="form-control"
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
                >
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
        (() => {
            const container = document.getElementById('detail-rows');
            const template = document.getElementById('detail-row-template');
            const addButton = document.getElementById('add-detail');

            if (!container || !template || !addButton) {
                return;
            }

            let nextIndex = Math.max(
                -1,
                ...Array.from(container.querySelectorAll('[data-detail-row]'))
                    .map((row) => {
                        const input = row.querySelector('[name*="[sku]"]');
                        const match = input?.name.match(/details\[(\d+)\]/);

                        return match ? Number(match[1]) : -1;
                    })
            ) + 1;

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex++)
                );

                const temporaryBody = document.createElement('tbody');
                temporaryBody.innerHTML = html.trim();

                const newRow = temporaryBody.firstElementChild;

                if (newRow) {
                    container.appendChild(newRow);
                }
            });

            container.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');

                if (!button) {
                    return;
                }

                const rows = container.querySelectorAll('[data-detail-row]');

                if (rows.length <= 1) {
                    alert('Minimal harus ada satu detail barang.');
                    return;
                }

                button.closest('[data-detail-row]').remove();
            });
        })();
    </script>
@endsection
