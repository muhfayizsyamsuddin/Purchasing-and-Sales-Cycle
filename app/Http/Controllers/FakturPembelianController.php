<?php

namespace App\Http\Controllers;

use App\Models\FakturPembelian;
use App\Models\MBarang;
use App\Models\PenerimaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class FakturPembelianController extends Controller
{
    public function index()
    {
        $fakturs = FakturPembelian::with([
            'penerimaan.pesanan.vendor',
        ])
            ->withSum('details', 'kuantitas')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('faktur-pembelian.index', compact('fakturs'));
    }

    public function create()
    {
        $penerimaans = PenerimaanBarang::with([
            'pesanan.vendor',
            'pesanan.details',
            'details.barang',
        ])
            ->orderByDesc('tanggal')
            ->get();

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'faktur-pembelian.create',
            compact('penerimaans', 'barangs')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules($request)
        );

        $this->validateReceiptDetails($validated);

        try {
            DB::transaction(function () use ($validated) {
                $jumlah = 0;

                foreach ($validated['details'] as $detail) {
                    $kuantitas = (int) $detail['kuantitas'];
                    $hargaUnit = (float) $detail['harga_unit'];
                    $diskon = (float) ($detail['diskon'] ?? 0);

                    $totalDetail = ($kuantitas * $hargaUnit) - $diskon;
                    $jumlah += $totalDetail;
                }

                $ppn = (float) ($validated['ppn'] ?? 0);

                $faktur = FakturPembelian::create([
                    'no_faktur' => $validated['no_faktur'],
                    'nama' => $validated['nama'],
                    'no_telp' => $validated['no_telp'],
                    'alamat' => $validated['alamat'],
                    'tanggal' => $validated['tanggal'],
                    'jangka_waktu_pembayaran' =>
                        $validated['jangka_waktu_pembayaran'],
                    'jumlah' => $jumlah,
                    'ppn' => $ppn,
                    'total_harga' => $jumlah + $ppn,
                    'no_penerimaan' => $validated['no_penerimaan'],
                ]);

                foreach ($validated['details'] as $detail) {
                    $kuantitas = (int) $detail['kuantitas'];
                    $hargaUnit = (float) $detail['harga_unit'];
                    $diskon = (float) ($detail['diskon'] ?? 0);

                    $faktur->details()->create([
                        'sku' => $detail['sku'],
                        'kuantitas' => $kuantitas,
                        'harga_unit' => $hargaUnit,
                        'diskon' => $diskon,
                        'total_harga' =>
                            ($kuantitas * $hargaUnit) - $diskon,
                    ]);
                }
            });

            return redirect()
                ->route('faktur-pembelian.index')
                ->with('success', 'Faktur pembelian berhasil disimpan.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Faktur pembelian gagal disimpan. Silakan coba lagi.'
                );
        }
    }

    public function edit(string $no_faktur)
    {
        $faktur = FakturPembelian::with('details')
            ->findOrFail($no_faktur);

        $penerimaans = PenerimaanBarang::with([
            'pesanan.vendor',
            'details.barang',
        ])
            ->orderByDesc('tanggal')
            ->get();

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'faktur-pembelian.edit',
            compact('faktur', 'penerimaans', 'barangs')
        );
    }

    public function update(Request $request, string $no_faktur)
    {
        $faktur = FakturPembelian::findOrFail($no_faktur);

        $validated = $request->validate(
            $this->validationRules($request, $faktur)
        );

        $this->validateReceiptDetails($validated);

        try {
            DB::transaction(function () use ($faktur, $validated) {
                $jumlah = 0;

                foreach ($validated['details'] as $detail) {
                    $kuantitas = (int) $detail['kuantitas'];
                    $hargaUnit = (float) $detail['harga_unit'];
                    $diskon = (float) ($detail['diskon'] ?? 0);

                    $jumlah += ($kuantitas * $hargaUnit) - $diskon;
                }

                $ppn = (float) ($validated['ppn'] ?? 0);

                $faktur->update([
                    'no_faktur' => $validated['no_faktur'],
                    'nama' => $validated['nama'],
                    'no_telp' => $validated['no_telp'],
                    'alamat' => $validated['alamat'],
                    'tanggal' => $validated['tanggal'],
                    'jangka_waktu_pembayaran' =>
                        $validated['jangka_waktu_pembayaran'],
                    'jumlah' => $jumlah,
                    'ppn' => $ppn,
                    'total_harga' => $jumlah + $ppn,
                    'no_penerimaan' => $validated['no_penerimaan'],
                ]);

                $faktur->details()->delete();

                foreach ($validated['details'] as $detail) {
                    $kuantitas = (int) $detail['kuantitas'];
                    $hargaUnit = (float) $detail['harga_unit'];
                    $diskon = (float) ($detail['diskon'] ?? 0);

                    $faktur->details()->create([
                        'sku' => $detail['sku'],
                        'kuantitas' => $kuantitas,
                        'harga_unit' => $hargaUnit,
                        'diskon' => $diskon,
                        'total_harga' =>
                            ($kuantitas * $hargaUnit) - $diskon,
                    ]);
                }
            });

            return redirect()
                ->route('faktur-pembelian.index')
                ->with('success', 'Faktur pembelian berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Faktur pembelian gagal diperbarui. Silakan coba lagi.'
                );
        }
    }

    public function destroy(string $no_faktur)
    {
        $faktur = FakturPembelian::findOrFail($no_faktur);

        try {
            DB::transaction(function () use ($faktur) {
                $faktur->delete();
            });

            return redirect()
                ->route('faktur-pembelian.index')
                ->with('success', 'Faktur pembelian berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Faktur pembelian gagal dihapus.'
            );
        }
    }

    private function validationRules(
        Request $request,
        ?FakturPembelian $existing = null
    ): array {
        $uniqueNoFaktur = Rule::unique(
            'faktur_pembelian',
            'no_faktur'
        );

        if ($existing !== null) {
            $uniqueNoFaktur->ignore(
                $existing->no_faktur,
                'no_faktur'
            );
        }

        $noPenerimaan = $request->input('no_penerimaan');

        return [
            'no_faktur' => [
                'required',
                'string',
                'max:255',
                $uniqueNoFaktur,
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'no_telp' => [
                'required',
                'string',
                'max:50',
            ],
            'alamat' => [
                'required',
                'string',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'jangka_waktu_pembayaran' => [
                'required',
                'string',
                'max:255',
            ],
            'no_penerimaan' => [
                'required',
                'string',
                'exists:penerimaan_barang,no_penerimaan',
            ],
            'jumlah' => [
                'prohibited',
            ],
            'total_harga' => [
                'prohibited',
            ],
            'ppn' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'details' => [
                'required',
                'array',
                'min:1',
            ],
            'details.*.sku' => [
                'required',
                'string',
                'distinct',
                Rule::exists('detail_m_penerimaan_barang', 'sku')
                    ->where('no_penerimaan', $noPenerimaan),
            ],
            'details.*.kuantitas' => [
                'required',
                'integer',
                'min:1',
            ],
            'details.*.harga_unit' => [
                'required',
                'numeric',
                'min:0',
            ],
            'details.*.diskon' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function cetak(string $no_faktur)
    {
        $faktur = FakturPembelian::with([
            'penerimaan.pesanan.vendor',
            'details.barang',
        ])->findOrFail($no_faktur);

        return view('faktur-pembelian.print', compact('faktur'));
    }

    private function validateReceiptDetails(array $validated): void
    {
        $penerimaan = PenerimaanBarang::with('details')
            ->findOrFail($validated['no_penerimaan']);

        $receiptDetails = $penerimaan->details->keyBy('sku');

        foreach ($validated['details'] as $index => $detail) {
            $sku = $detail['sku'];
            $kuantitas = (int) $detail['kuantitas'];
            $hargaUnit = (float) $detail['harga_unit'];
            $diskon = (float) ($detail['diskon'] ?? 0);

            if (! $receiptDetails->has($sku)) {
                throw ValidationException::withMessages([
                    "details.$index.sku" =>
                        'Barang tidak tercantum pada penerimaan yang dipilih.',
                ]);
            }

            $kuantitasDiterima = (int) $receiptDetails->get($sku)->kuantitas;

            if ($kuantitas > $kuantitasDiterima) {
                throw ValidationException::withMessages([
                    "details.$index.kuantitas" =>
                        "Kuantitas faktur tidak boleh melebihi kuantitas diterima ({$kuantitasDiterima}).",
                ]);
            }

            if ($diskon > ($kuantitas * $hargaUnit)) {
                throw ValidationException::withMessages([
                    "details.$index.diskon" =>
                        'Diskon tidak boleh melebihi total harga sebelum diskon.',
                ]);
            }
        }
    }
}
