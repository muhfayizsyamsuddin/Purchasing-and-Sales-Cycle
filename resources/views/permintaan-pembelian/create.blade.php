
@extends('gentelella::page')

@section('title', 'Buat Permintaan Pembelian')
@section('page_key', 'permintaan-pembelian')
@section('breadcrumb', 'Home > Transaksi > Permintaan Pembelian > Tambah')

@section('content')
    <div class="permintaan-form-page">
        <x-gentelella::page-header
            title="Buat Permintaan Pembelian"
            pretitle="Purchasing Cycle"
        />

        <style>
            .permintaan-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .permintaan-form-grid .form-group {
                min-width: 0;
                margin-bottom: 0;
            }

            .permintaan-form-page .form-control {
                width: 100%;
                box-sizing: border-box;
            }

            .permintaan-detail-section {
                margin-top: 28px;
                padding-top: 24px;
                border-top: 1px solid var(--border-color);
            }

            .permintaan-detail-heading {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 16px;
            }

            .permintaan-detail-heading h3 {
                margin: 0 0 4px;
                font-size: 17px;
                font-weight: 600;
            }

            .permintaan-detail-table {
                width: 100%;
                min-width: 680px;
                table-layout: fixed;
            }

            .permintaan-detail-table th,
            .permintaan-detail-table td {
                padding: 12px;
                vertical-align: middle;
            }

            .permintaan-detail-table th {
                text-align: center;
            }

            .permintaan-detail-table th:nth-child(1) {
                width: 42%;
            }

            .permintaan-detail-table th:nth-child(2) {
                width: 16%;
            }

            .permintaan-detail-table th:nth-child(3) {
                width: 27%;
            }

            .permintaan-detail-table th:nth-child(4) {
                width: 15%;
            }

            .permintaan-detail-table .form-control {
                min-width: 0;
            }

            .permintaan-form-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }

            @media (max-width: 700px) {
                .permintaan-form-grid {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 16px;
                }

                .permintaan-form-actions {
                    flex-wrap: wrap;
                }
            }
        </style>

        <x-gentelella::card title="Informasi Permintaan">
            <p style="color:var(--text-muted); font-size:13px; margin:0 0 24px;">
                Lengkapi informasi permintaan pembelian dan barang yang dibutuhkan.
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

            <form
                action="{{ route('permintaan-pembelian.store') }}"
                method="POST"
            >
                @csrf

                <div class="permintaan-form-grid">
                    <div class="form-group">
                        <label for="no_faktur" class="form-label">
                            No. Permintaan <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_faktur"
                            name="no_faktur"
                            value="{{ old('no_faktur') }}"
                            class="form-control @error('no_faktur') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Masukkan nomor faktur"
                            required
                        >

                        @error('no_faktur')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal" class="form-label">
                            Tanggal Permintaan <span class="required">*</span>
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
                        <label for="tanggal_diperlukan" class="form-label">
                            Tanggal Diperlukan <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="tanggal_diperlukan"
                            name="tanggal_diperlukan"
                            value="{{ old('tanggal_diperlukan') }}"
                            class="form-control @error('tanggal_diperlukan') is-invalid @enderror"
                            required
                        >

                        @error('tanggal_diperlukan')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="permintaan-detail-section">
                    <div class="permintaan-detail-heading">
                        <div>
                            <h3>Detail Barang</h3>
                            <p style="color:var(--text-muted); font-size:13px; margin:0;">
                                Pilih barang dan masukkan kuantitas yang diperlukan.
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
                        <table class="table permintaan-detail-table">
                            <thead>
                                <tr>
                                    <th>SKU / Barang</th>
                                    <th>Kuantitas</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="detail-rows">
                                @foreach (old('details', [['sku' => '', 'kuantitas' => 1, 'keterangan' => '']]) as $index => $detail)
                                    <tr data-detail-row>
                                        <td>
                                            <select
                                                name="details[{{ $index }}][sku]"
                                                class="form-control"
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
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="details[{{ $index }}][kuantitas]"
                                                class="form-control"
                                                min="1"
                                                value="{{ $detail['kuantitas'] ?? 1 }}"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="text"
                                                name="details[{{ $index }}][keterangan]"
                                                class="form-control"
                                                value="{{ $detail['keterangan'] ?? '' }}"
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="permintaan-form-actions">
                    <a
                        href="{{ route('permintaan-pembelian.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Permintaan
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
                    type="text"
                    name="details[__INDEX__][keterangan]"
                    class="form-control"
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
