@extends('gentelella::page')

@section('title', 'Edit Permintaan Pembelian')
@section('page_key', 'permintaan-pembelian')
@section('breadcrumb', 'Home > Transaksi > Permintaan Pembelian > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit Permintaan Pembelian"
        pretitle="Purchasing Cycle"
    />

    <x-gentelella::card title="Informasi Permintaan">
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
            $details = old(
                'details',
                $permintaan->details->map(fn ($detail) => [
                    'sku' => $detail->sku,
                    'kuantitas' => $detail->kuantitas,
                    'keterangan' => $detail->keterangan,
                ])->all()
            );
        @endphp

        <form
            action="{{ route('permintaan-pembelian.update', $permintaan->no_faktur) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_faktur" class="form-label">
                    No. Faktur <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="no_faktur"
                    name="no_faktur"
                    value="{{ old('no_faktur', $permintaan->no_faktur) }}"
                    class="form-control @error('no_faktur') is-invalid @enderror"
                    maxlength="255"
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
                    value="{{ old('tanggal', date('Y-m-d', strtotime($permintaan->tanggal))) }}"
                    class="form-control"
                    required
                >
            </div>

            <div class="form-group">
                <label for="tanggal_diperlukan" class="form-label">
                    Tanggal Diperlukan <span class="required">*</span>
                </label>

                <input
                    type="date"
                    id="tanggal_diperlukan"
                    name="tanggal_diperlukan"
                    value="{{ old('tanggal_diperlukan', date('Y-m-d', strtotime($permintaan->tanggal_diperlukan))) }}"
                    class="form-control"
                    required
                >
            </div>

            <hr>

            <h3>Detail Barang</h3>

            <div style="overflow-x: auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>SKU / Barang</th>
                            <th>Kuantitas</th>
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
                                        required
                                    >
                                        <option value="">Pilih barang</option>

                                        @foreach ($barangs as $barang)
                                            <option
                                                value="{{ $barang->sku }}"
                                                @selected(($detail['sku'] ?? '') === $barang->sku)
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
                    href="{{ route('permintaan-pembelian.index') }}"
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
        (() => {
            const container = document.getElementById('detail-rows');
            const template = document.getElementById('detail-row-template');
            const addButton = document.getElementById('add-detail');

            let nextIndex = container.querySelectorAll('[data-detail-row]').length;

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll(
                    '__INDEX__',
                    String(nextIndex++)
                );

                const temporaryBody = document.createElement('tbody');
                temporaryBody.innerHTML = html.trim();

                container.appendChild(temporaryBody.firstElementChild);
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