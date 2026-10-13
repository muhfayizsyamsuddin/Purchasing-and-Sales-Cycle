@extends('gentelella::page')

@section('title', 'Edit Pembayaran Pembelian')
@section('page_key', 'pembayaran-pembelian')
@section('breadcrumb', 'Home > Purchasing Cycle > Pembayaran Pembelian > Edit')

@section('content')
    @php
        $fakturOptions = $fakturs->map(function ($faktur) use ($pembayaran) {
            $sudahDibayar = (float) $faktur->pembayaran->sum('jumlah');

            // Keluarkan pembayaran yang sedang diedit dari perhitungan.
            if ($faktur->no_faktur === $pembayaran->no_faktur) {
                $sudahDibayar -= (float) $pembayaran->jumlah;
            }

            $total = (float) $faktur->total_harga;
            $sisa = max($total - $sudahDibayar, 0);

            return [
                'value' => $faktur->no_faktur,
                'label' => $faktur->no_faktur . ' — ' . $faktur->nama,
                'nama' => $faktur->nama,
                'total' => $total,
                'terbayar' => max($sudahDibayar, 0),
                'sisa' => $sisa,
            ];
        })->values()->all();
    @endphp

    <x-gentelella::page-header
        title="Edit Pembayaran Pembelian"
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
            action="{{ route('pembayaran-pembelian.update', $pembayaran->no_pembayaran) }}"
            method="POST"
            id="pembayaran-form"
        >
            @csrf
            @method('PUT')

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
                            @selected(old('no_faktur', $pembayaran->no_faktur) === $faktur['value'])
                            @disabled($faktur['sisa'] <= 0 && $faktur['value'] !== $pembayaran->no_faktur)
                        >
                            {{ $faktur['label'] }}{{ $faktur['sisa'] <= 0 && $faktur['value'] !== $pembayaran->no_faktur ? ' — LUNAS' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('no_faktur')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div
                id="tagihan-summary"
                style="display: none; margin-bottom: 20px; padding: 14px; border: 1px solid #ddd; border-radius: 4px"
            >
                <h4 style="margin-top: 0">Ringkasan Tagihan</h4>

                <div style="display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap">
                    <div>
                        <div class="muted">Total Faktur</div>
                        <strong id="total-faktur">Rp0,00</strong>
                    </div>
                    <div>
                        <div class="muted">Sudah Dibayar (di luar pembayaran ini)</div>
                        <strong id="sudah-dibayar">Rp0,00</strong>
                    </div>
                    <div>
                        <div class="muted">Sisa Tagihan</div>
                        <strong id="sisa-tagihan">Rp0,00</strong>
                    </div>
                </div>

                <p id="tagihan-help" style="margin: 12px 0 0"></p>
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
                <small class="muted">
                    Jumlah tidak boleh melebihi sisa tagihan sebelum pembayaran ini.
                </small>
                @error('jumlah')
                    <div class="form-error">{{ $message }}</div>
                @enderror
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

            <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('pembayaran-pembelian.index') }}"
                    class="btn btn-outline"
                >
                    Batal
                </a>
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
                        ? 'Tidak ada sisa tagihan untuk faktur ini.'
                        : 'Batas jumlah pembayaran untuk faktur ini: '
                            + formatRupiah(selected.sisa);
                }

                fakturSelect.addEventListener('change', function () {
                    const selected = fakturOptions.find(
                        faktur => faktur.value === fakturSelect.value
                    );

                    terimaDari.value = selected ? selected.nama : '';
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
@endsection