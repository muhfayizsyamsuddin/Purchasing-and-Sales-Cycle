
@extends('gentelella::page')

@section('title', 'Tambah Pembayaran Pembelian')
@section('page_key', 'pembayaran-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pembayaran Pembelian > Tambah')

@section('content')
    @php
        $fakturOptions = $fakturs->map(function ($faktur) {
            $sudahDibayar = (float) $faktur->pembayaran->sum('jumlah');

            return [
                'value' => $faktur->no_faktur,
                'label' => $faktur->no_faktur . ' — ' . $faktur->nama,
                'nama' => $faktur->nama,
                'total' => (float) $faktur->total_harga,
                'terbayar' => $sudahDibayar,
                'sisa' => max((float) $faktur->total_harga - $sudahDibayar, 0),
            ];
        })->values()->all();
    @endphp

    <style>
        .pembayaran-pembelian-form-page .form-section {
            margin-bottom: 24px;
        }

        .pembayaran-pembelian-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .pembayaran-pembelian-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .pembayaran-pembelian-form-page .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .pembayaran-pembelian-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .pembayaran-pembelian-form-page .required {
            color: #dc3545;
        }

        .pembayaran-pembelian-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .pembayaran-pembelian-form-page .form-help {
            margin-top: 8px;
            color: #73808c;
            font-size: 12px;
        }

        .pembayaran-pembelian-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .pembayaran-pembelian-form-page .section-description {
            margin: 0 0 18px;
            color: #73808c;
            font-size: 13px;
        }

        .pembayaran-pembelian-form-page .tagihan-summary {
            margin: 22px 0;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #f8fafc;
        }

        .pembayaran-pembelian-form-page .tagihan-summary h4 {
            margin: 0 0 18px;
            font-size: 16px;
            font-weight: 600;
        }

        .pembayaran-pembelian-form-page .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .pembayaran-pembelian-form-page .summary-item {
            min-width: 0;
        }

        .pembayaran-pembelian-form-page .summary-label {
            margin-bottom: 6px;
            color: #64748b;
            font-size: 13px;
        }

        .pembayaran-pembelian-form-page .summary-value {
            overflow-wrap: anywhere;
            font-size: 17px;
            font-weight: 600;
        }

        .pembayaran-pembelian-form-page .summary-help {
            margin: 16px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .pembayaran-pembelian-form-page .form-actions {
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
            .pembayaran-pembelian-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .pembayaran-pembelian-form-page .summary-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 14px;
            }
        }
    </style>

    <div class="pembayaran-pembelian-form-page">
        <x-gentelella::page-header
            title="Tambah Pembayaran Pembelian"
            pretitle="Purchasing Cycle"
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
                action="{{ route('pembayaran-pembelian.store') }}"
                method="POST"
                id="pembayaran-form"
            >
                @csrf

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
                                value="{{ old('no_pembayaran') }}"
                                class="form-control"
                                maxlength="255"
                                placeholder="Contoh: PBAY-2026-001"
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
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                class="form-control"
                                required
                            >
                            @error('tanggal')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="no_faktur" class="form-label">
                                Faktur Pembelian <span class="required">*</span>
                            </label>
                            <select
                                id="no_faktur"
                                name="no_faktur"
                                class="form-control"
                                required
                            >
                                <option value="">Pilih faktur pembelian</option>
                                @foreach ($fakturOptions as $faktur)
                                    <option
                                        value="{{ $faktur['value'] }}"
                                        @selected(old('no_faktur') === $faktur['value'])
                                        @disabled($faktur['sisa'] <= 0)
                                    >
                                        {{ $faktur['label'] }}{{ $faktur['sisa'] <= 0 ? ' — LUNAS' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('no_faktur')
                                <div class="form-error">{{ $message }}</div>
                            @enderror

                            @if (count($fakturOptions) === 0)
                                <p class="form-help">
                                    Belum ada faktur pembelian. Buat faktur terlebih dahulu.
                                </p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="terima_dari" class="form-label">
                                Terima Dari <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="terima_dari"
                                name="terima_dari"
                                value="{{ old('terima_dari') }}"
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
                                <div class="summary-label">Sudah Dibayar</div>
                                <div class="summary-value" id="sudah-dibayar">Rp0,00</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Sisa Tagihan</div>
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
                                value="{{ old('jumlah') }}"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                required
                            >
                            @error('jumlah')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-help">
                                Jumlah pembayaran tidak boleh melebihi sisa tagihan faktur.
                            </div>
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
                            >{{ old('terbilang') }}</textarea>
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
                                placeholder="Contoh: Pembayaran faktur pembelian FP-2026-001"
                            >{{ old('untuk_pembayaran') }}</textarea>
                            @error('untuk_pembayaran')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('pembayaran-pembelian.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        @disabled(count($fakturOptions) === 0)
                    >
                        Simpan Pembayaran
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

                    function updateTagihan() {
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
                            ? 'Faktur ini sudah lunas dan tidak menerima pembayaran tambahan.'
                            : 'Pembayaran maksimal: ' + formatRupiah(selected.sisa);

                        if (!terimaDari.value.trim()) {
                            terimaDari.value = selected.nama;
                        }
                    }

                    fakturSelect.addEventListener('change', function () {
                        terimaDari.value = '';
                        updateTagihan();
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
