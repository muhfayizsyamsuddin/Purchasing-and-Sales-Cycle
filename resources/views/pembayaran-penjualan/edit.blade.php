
@extends('gentelella::page')

@section('title', 'Edit Pembayaran Penjualan')
@section('page_key', 'pembayaran-penjualan')
@section('breadcrumb', 'Home > Sales Cycle > Pembayaran Penjualan > Edit')

@section('content')
    @php
        $fakturOptions = $fakturs->map(function ($faktur) {
            return [
                'value' => $faktur->no_faktur,
                'label' => $faktur->no_faktur . ' — ' . $faktur->nama,
                'nama' => $faktur->nama,
                'total' => (float) $faktur->total_harga,
                'terbayar' => (float) $faktur->jumlah_terbayar,
                'sisa' => (float) $faktur->sisa_tagihan,
            ];
        })->values()->all();
    @endphp

    <style>
        .pembayaran-penjualan-form-page .form-section {
            margin-bottom: 24px;
        }

        .pembayaran-penjualan-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .pembayaran-penjualan-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .pembayaran-penjualan-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .pembayaran-penjualan-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .pembayaran-penjualan-form-page .required {
            color: #dc3545;
        }

        .pembayaran-penjualan-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .pembayaran-penjualan-form-page .form-help {
            display: block;
            margin-top: 8px;
            color: #73808c;
            font-size: 12px;
        }

        .pembayaran-penjualan-form-page .tagihan-summary {
            margin: 22px 0;
            padding: 20px;
            border: 1px solid #344256;
            border-radius: 6px;
            background: #1c293a;
            color: #f1f5f9;
        }

        .pembayaran-penjualan-form-page .tagihan-summary h4 {
            margin: 0 0 18px;
            color: #f1f5f9;
            font-size: 16px;
            font-weight: 600;
        }

        .pembayaran-penjualan-form-page .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .pembayaran-penjualan-form-page .summary-item {
            min-width: 0;
        }

        .pembayaran-penjualan-form-page .summary-label {
            margin-bottom: 6px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .pembayaran-penjualan-form-page .summary-value {
            overflow-wrap: anywhere;
            color: #ffffff;
            font-size: 17px;
            font-weight: 600;
        }

        .pembayaran-penjualan-form-page .summary-help {
            margin: 16px 0 0;
            color: #cbd5e1;
            font-size: 13px;
        }

        .pembayaran-penjualan-form-page .form-actions {
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
            .pembayaran-penjualan-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .pembayaran-penjualan-form-page .summary-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 14px;
            }
        }
    </style>

    <div class="pembayaran-penjualan-form-page">
        <x-gentelella::page-header
            title="Edit Pembayaran Penjualan"
            pretitle="Sales Cycle"
        />

        <x-gentelella::card title="Informasi Pembayaran">
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
                action="{{ route('pembayaran-penjualan.update', $pembayaran->no_pembayaran) }}"
                method="POST"
                id="pembayaran-form"
            >
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="no_pembayaran" class="form-label">
                                No. Pembayaran <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="no_pembayaran"
                                name="no_pembayaran"
                                value="{{ old('no_pembayaran', $pembayaran->no_pembayaran) }}"
                                class="form-control"
                                maxlength="255"
                                required
                            >
                            @error('no_pembayaran')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tanggal" class="form-label">
                                Tanggal Pembayaran <span class="required">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                value="{{ old('tanggal', $pembayaran->tanggal->format('Y-m-d')) }}"
                                class="form-control"
                                required
                            >
                            @error('tanggal')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="no_faktur" class="form-label">
                                Faktur Penjualan <span class="required">*</span>
                            </label>
                            <select
                                id="no_faktur"
                                name="no_faktur"
                                class="form-control"
                                required
                            >
                                <option value="">Pilih faktur penjualan</option>
                                @foreach ($fakturOptions as $faktur)
                                    <option
                                        value="{{ $faktur['value'] }}"
                                        @selected(old('no_faktur', $pembayaran->no_faktur) === $faktur['value'])
                                        @disabled($faktur['sisa'] <= 0 && $faktur['value'] !== $pembayaran->no_faktur)
                                    >
                                        {{ $faktur['label'] }}
                                        {{ $faktur['sisa'] <= 0 && $faktur['value'] === $pembayaran->no_faktur ? ' — LUNAS (PEMBAYARAN INI)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('no_faktur')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <span class="form-help">
                                Ringkasan memperhitungkan nilai pembayaran lain pada faktur.
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="terima_dari" class="form-label">
                                Terima Dari <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="terima_dari"
                                name="terima_dari"
                                value="{{ old('terima_dari', $pembayaran->terima_dari) }}"
                                class="form-control"
                                maxlength="255"
                                required
                            >
                            @error('terima_dari')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div
                        id="tagihan-summary"
                        class="tagihan-summary"
                        style="display: none"
                    >
                        <h4>Ringkasan Tagihan</h4>

                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="summary-label">Total Faktur</div>
                                <div class="summary-value" id="total-faktur">Rp0,00</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label">Pembayaran Lain</div>
                                <div class="summary-value" id="sudah-dibayar">Rp0,00</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label">Sisa Tagihan untuk Pembayaran Ini</div>
                                <div class="summary-value" id="sisa-tagihan">Rp0,00</div>
                            </div>
                        </div>

                        <p id="tagihan-help" class="summary-help"></p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="jumlah" class="form-label">
                                Jumlah Pembayaran <span class="required">*</span>
                            </label>
                            <input
                                type="number"
                                id="jumlah"
                                name="jumlah"
                                value="{{ old('jumlah', $pembayaran->jumlah) }}"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                required
                            >
                            @error('jumlah')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <span class="form-help">
                                Jumlah tidak boleh melebihi sisa tagihan setelah pembayaran lain diperhitungkan.
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="terbilang" class="form-label">
                                Terbilang <span class="required">*</span>
                            </label>
                            <textarea
                                id="terbilang"
                                name="terbilang"
                                class="form-control"
                                rows="2"
                                required
                            >{{ old('terbilang', $pembayaran->terbilang) }}</textarea>
                            @error('terbilang')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="untuk_pembayaran" class="form-label">
                                Untuk Pembayaran <span class="required">*</span>
                            </label>
                            <textarea
                                id="untuk_pembayaran"
                                name="untuk_pembayaran"
                                class="form-control"
                                rows="3"
                                required
                            >{{ old('untuk_pembayaran', $pembayaran->untuk_pembayaran) }}</textarea>
                            @error('untuk_pembayaran')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('pembayaran-penjualan.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const fakturSelect = document.getElementById('no_faktur');
                    const terimaDari = document.getElementById('terima_dari');
                    const jumlahInput = document.getElementById('jumlah');
                    const summary = document.getElementById('tagihan-summary');
                    const totalElement = document.getElementById('total-faktur');
                    const paidElement = document.getElementById('sudah-dibayar');
                    const remainingElement = document.getElementById('sisa-tagihan');
                    const helpText = document.getElementById('tagihan-help');

                    const fakturOptions = {{ \Illuminate\Support\Js::from($fakturOptions) }};

                    function formatRupiah(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(value);
                    }

                    function updateTagihan(resetNama = false) {
                        const selected = fakturOptions.find(
                            faktur => faktur.value === fakturSelect.value
                        );

                        if (!selected) {
                            summary.style.display = 'none';
                            jumlahInput.removeAttribute('max');
                            jumlahInput.setCustomValidity('');
                            return;
                        }

                        summary.style.display = 'block';
                        totalElement.textContent = formatRupiah(selected.total);
                        paidElement.textContent = formatRupiah(selected.terbayar);
                        remainingElement.textContent = formatRupiah(selected.sisa);

                        jumlahInput.max = selected.sisa.toFixed(2);

                        if (Number(jumlahInput.value) > selected.sisa) {
                            jumlahInput.setCustomValidity(
                                'Jumlah pembayaran melebihi sisa tagihan.'
                            );
                        } else {
                            jumlahInput.setCustomValidity('');
                        }

                        helpText.textContent = selected.sisa <= 0
                            ? 'Tidak ada sisa tagihan selain pembayaran ini.'
                            : 'Maksimal pembayaran yang diizinkan: '
                                + formatRupiah(selected.sisa);

                        if (resetNama || !terimaDari.value.trim()) {
                            terimaDari.value = selected.nama;
                        }
                    }

                    fakturSelect.addEventListener('change', function () {
                        updateTagihan(true);
                    });

                    jumlahInput.addEventListener('input', function () {
                        const selected = fakturOptions.find(
                            faktur => faktur.value === fakturSelect.value
                        );

                        if (selected && Number(jumlahInput.value) > selected.sisa) {
                            jumlahInput.setCustomValidity(
                                'Jumlah pembayaran melebihi sisa tagihan.'
                            );
                        } else {
                            jumlahInput.setCustomValidity('');
                        }
                    });

                    updateTagihan();
                });
            </script>
        </x-gentelella::card>
    </div>
@endsection
