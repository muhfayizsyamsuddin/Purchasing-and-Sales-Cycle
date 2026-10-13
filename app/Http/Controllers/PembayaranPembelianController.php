<?php

namespace App\Http\Controllers;

use App\Models\FakturPembelian;
use App\Models\PembayaranPembelian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembayaranPembelianController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPembelian::with('faktur')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view(
            'pembayaran-pembelian.index',
            compact('pembayarans')
        );
    }

    public function create()
    {
        $fakturs = FakturPembelian::with('pembayaran')
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'pembayaran-pembelian.create',
            compact('fakturs')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules()
        );

        $this->validateJumlahPembayaran($validated);

        PembayaranPembelian::create($validated);

        return redirect()
            ->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil disimpan.');
    }

    public function edit(string $no_pembayaran)
    {
        $pembayaran = PembayaranPembelian::findOrFail($no_pembayaran);
        $fakturs = FakturPembelian::with('pembayaran')
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'pembayaran-pembelian.edit',
            compact('pembayaran', 'fakturs')
        );
    }

    public function update(
        Request $request,
        string $no_pembayaran
    ) {
        $pembayaran = PembayaranPembelian::findOrFail($no_pembayaran);

        $validated = $request->validate(
            $this->validationRules($pembayaran)
        );

        $this->validateJumlahPembayaran($validated, $pembayaran);

        $pembayaran->update($validated);

        return redirect()
            ->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil diperbarui.');
    }

    public function destroy(string $no_pembayaran)
    {
        $pembayaran = PembayaranPembelian::findOrFail($no_pembayaran);

        $pembayaran->delete();

        return redirect()
            ->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil dihapus.');
    }

    public function cetak(string $no_pembayaran)
    {
        $pembayaran = PembayaranPembelian::with('faktur')
            ->findOrFail($no_pembayaran);

        return view(
            'pembayaran-pembelian.print',
            compact('pembayaran')
        );
    }

    private function validationRules(
        ?PembayaranPembelian $pembayaran = null
    ): array {
        $noPembayaranRule = Rule::unique(
            'pembayaran_pembelian',
            'no_pembayaran'
        );

        if ($pembayaran !== null) {
            $noPembayaranRule->ignore(
                $pembayaran->no_pembayaran,
                'no_pembayaran'
            );
        }

        return [
            'no_pembayaran' => [
                'required',
                'string',
                'max:255',
                $noPembayaranRule,
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'terima_dari' => [
                'required',
                'string',
                'max:255',
            ],
            'terbilang' => [
                'required',
                'string',
            ],
            'untuk_pembayaran' => [
                'required',
                'string',
            ],
            'jumlah' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'no_faktur' => [
                'required',
                'string',
                'exists:faktur_pembelian,no_faktur',
            ],
        ];
    }

    private function validateJumlahPembayaran(
        array $validated,
        ?PembayaranPembelian $pembayaran = null
    ): void {
        $faktur = FakturPembelian::findOrFail(
            $validated['no_faktur']
        );

        $sudahDibayar = (float) $faktur->pembayaran()->sum('jumlah');

        // Saat mengedit pembayaran pada faktur yang sama,
        // keluarkan nilai pembayaran lama dari perhitungan.
        if (
            $pembayaran !== null
            && $pembayaran->no_faktur === $faktur->no_faktur
        ) {
            $sudahDibayar -= (float) $pembayaran->jumlah;
        }

        $sisaTagihan = (float) $faktur->total_harga - $sudahDibayar;
        $jumlahPembayaran = (float) $validated['jumlah'];

        if ($jumlahPembayaran > $sisaTagihan + 0.00001) {
            $sisaFormat = number_format(
                max($sisaTagihan, 0),
                2,
                ',',
                '.'
            );

            throw ValidationException::withMessages([
                'jumlah' => "Jumlah pembayaran melebihi sisa tagihan faktur. Sisa tagihan saat ini Rp {$sisaFormat}.",
            ]);
        }
    }
}
