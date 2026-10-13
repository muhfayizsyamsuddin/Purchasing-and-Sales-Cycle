<?php

namespace App\Http\Controllers;

use App\Models\DetailMReturPembelian;
use App\Models\PenerimaanBarang;
use App\Models\ReturPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReturPembelianController extends Controller
{
    public function index()
    {
        $returs = ReturPembelian::with([
            'penerimaan.pesanan.vendor',
        ])
            ->withCount('details')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('retur-pembelian.index', compact('returs'));
    }

    public function create()
    {
        $penerimaans = $this->getPenerimaans();

        return view('retur-pembelian.create', compact('penerimaans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules($request)
        );

        $this->validateReceiptDetails($validated);

        DB::transaction(function () use ($validated) {
            [$jumlah, $ppn, $totalHarga] = $this->calculateTotals($validated);

            $retur = ReturPembelian::create([
                'no_retur' => $validated['no_retur'],
                'no_penerimaan' => $validated['no_penerimaan'],
                'tanggal' => $validated['tanggal'],
                'nama_pembeli' => $validated['nama_pembeli'],
                'alamat_pembeli' => $validated['alamat_pembeli'],
                'nama_penjual' => $validated['nama_penjual'],
                'alamat_penjual' => $validated['alamat_penjual'],
                'jumlah' => $jumlah,
                'ppn' => $ppn,
                'total_harga' => $totalHarga,
            ]);

            foreach ($validated['details'] as $detail) {
                $kuantitas = (int) $detail['jumlah'];
                $hargaUnit = (float) $detail['harga_unit'];
                $diskon = (float) ($detail['diskon'] ?? 0);

                $retur->details()->create([
                    'sku' => $detail['sku'],
                    'jumlah' => $kuantitas,
                    'harga_unit' => $hargaUnit,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $hargaUnit) - $diskon,
                ]);
            }
        });

        return redirect()
            ->route('retur-pembelian.index')
            ->with('success', 'Retur pembelian berhasil disimpan.');
    }

    public function edit(string $no_retur)
    {
        $retur = ReturPembelian::with('details.barang')
            ->findOrFail($no_retur);

        $penerimaans = $this->getPenerimaans();

        return view(
            'retur-pembelian.edit',
            compact('retur', 'penerimaans')
        );
    }

    public function update(Request $request, string $no_retur)
    {
        $retur = ReturPembelian::findOrFail($no_retur);

        $validated = $request->validate(
            $this->validationRules($request, $retur)
        );

        $this->validateReceiptDetails($validated, $retur);

        DB::transaction(function () use ($validated, $retur) {
            [$jumlah, $ppn, $totalHarga] = $this->calculateTotals($validated);

            $retur->update([
                'no_retur' => $validated['no_retur'],
                'no_penerimaan' => $validated['no_penerimaan'],
                'tanggal' => $validated['tanggal'],
                'nama_pembeli' => $validated['nama_pembeli'],
                'alamat_pembeli' => $validated['alamat_pembeli'],
                'nama_penjual' => $validated['nama_penjual'],
                'alamat_penjual' => $validated['alamat_penjual'],
                'jumlah' => $jumlah,
                'ppn' => $ppn,
                'total_harga' => $totalHarga,
            ]);

            $retur->details()->delete();

            foreach ($validated['details'] as $detail) {
                $kuantitas = (int) $detail['jumlah'];
                $hargaUnit = (float) $detail['harga_unit'];
                $diskon = (float) ($detail['diskon'] ?? 0);

                $retur->details()->create([
                    'sku' => $detail['sku'],
                    'jumlah' => $kuantitas,
                    'harga_unit' => $hargaUnit,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $hargaUnit) - $diskon,
                ]);
            }
        });

        return redirect()
            ->route('retur-pembelian.index')
            ->with('success', 'Retur pembelian berhasil diperbarui.');
    }

    public function destroy(string $no_retur)
    {
        $retur = ReturPembelian::findOrFail($no_retur);
        $retur->delete();

        return redirect()
            ->route('retur-pembelian.index')
            ->with('success', 'Retur pembelian berhasil dihapus.');
    }

    public function cetak(string $no_retur)
    {
        $retur = ReturPembelian::with([
            'penerimaan.pesanan.vendor',
            'details.barang',
        ])->findOrFail($no_retur);

        return view('retur-pembelian.print', compact('retur'));
    }

    private function getPenerimaans()
    {
        return PenerimaanBarang::with([
            'pesanan.vendor',
            'pesanan.details',
            'details.barang',
            'returPembelian.details',
        ])
            ->orderByDesc('tanggal')
            ->get();
    }

    private function validationRules(
        Request $request,
        ?ReturPembelian $retur = null
    ): array {
        $noReturRule = Rule::unique('retur_pembelian', 'no_retur');

        if ($retur !== null) {
            $noReturRule->ignore($retur->no_retur, 'no_retur');
        }

        $noPenerimaan = $request->input('no_penerimaan');

        return [
            'no_retur' => [
                'required',
                'string',
                'max:255',
                $noReturRule,
            ],
            'no_penerimaan' => [
                'required',
                'string',
                'exists:penerimaan_barang,no_penerimaan',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'nama_pembeli' => [
                'required',
                'string',
                'max:255',
            ],
            'alamat_pembeli' => [
                'required',
                'string',
            ],
            'nama_penjual' => [
                'required',
                'string',
                'max:255',
            ],
            'alamat_penjual' => [
                'required',
                'string',
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
            'details.*.jumlah' => [
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

    private function validateReceiptDetails(
        array $validated,
        ?ReturPembelian $retur = null
    ): void {
        $penerimaan = PenerimaanBarang::with('details')
            ->findOrFail($validated['no_penerimaan']);

        $receiptDetails = $penerimaan->details->keyBy('sku');

        foreach ($validated['details'] as $index => $detail) {
            $sku = $detail['sku'];
            $kuantitasRetur = (int) $detail['jumlah'];
            $hargaUnit = (float) $detail['harga_unit'];
            $diskon = (float) ($detail['diskon'] ?? 0);

            if (! $receiptDetails->has($sku)) {
                throw ValidationException::withMessages([
                    "details.$index.sku" =>
                        'Barang tidak tercantum pada penerimaan yang dipilih.',
                ]);
            }

            $kuantitasDiterima = (int) $receiptDetails->get($sku)->kuantitas;

            $sudahDiretur = DetailMReturPembelian::where('sku', $sku)
                ->whereHas('retur', function ($query) use ($penerimaan, $retur) {
                    $query->where('no_penerimaan', $penerimaan->no_penerimaan);

                    if ($retur !== null) {
                        $query->where('no_retur', '!=', $retur->no_retur);
                    }
                })
                ->sum('jumlah');

            if ($kuantitasRetur + (int) $sudahDiretur > $kuantitasDiterima) {
                $kuantitasTersedia = max(
                    $kuantitasDiterima - (int) $sudahDiretur,
                    0
                );

                throw ValidationException::withMessages([
                    "details.$index.jumlah" =>
                        "Jumlah retur melebihi kuantitas yang masih bisa diretur ({$kuantitasTersedia}).",
                ]);
            }

            if ($diskon > ($kuantitasRetur * $hargaUnit)) {
                throw ValidationException::withMessages([
                    "details.$index.diskon" =>
                        'Diskon tidak boleh melebihi total harga sebelum diskon.',
                ]);
            }
        }
    }

    private function calculateTotals(array $validated): array
    {
        $jumlah = 0;

        foreach ($validated['details'] as $detail) {
            $kuantitas = (int) $detail['jumlah'];
            $hargaUnit = (float) $detail['harga_unit'];
            $diskon = (float) ($detail['diskon'] ?? 0);

            $jumlah += ($kuantitas * $hargaUnit) - $diskon;
        }

        $ppn = (float) ($validated['ppn'] ?? 0);

        return [
            $jumlah,
            $ppn,
            $jumlah + $ppn,
        ];
    }
}
