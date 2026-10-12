<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\MVendor;
use App\Models\PesananPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PesananPembelianController extends Controller
{
    public function index()
    {
        $pesanans = PesananPembelian::with(['vendor', 'details'])
            ->latest('tanggal')
            ->paginate(10);

        return view('pesanan-pembelian.index', compact('pesanans'));
    }

    public function create()
    {
        $vendors = MVendor::orderBy('nama_vendor')->get();
        $barangs = MBarang::orderBy('nama_barang')->get();

        return view('pesanan-pembelian.create', compact('vendors', 'barangs'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateInput($request);

        try {
            DB::beginTransaction();

            $jumlah = $this->calculateSubtotal($validated['details']);
            $ppn = (float) ($validated['ppn'] ?? 0);
            $totalHarga = $jumlah + $ppn;

            $pesanan = PesananPembelian::create([
                'no_order_pembelian' => $validated['no_order_pembelian'],
                'tanggal' => $validated['tanggal'],
                'tanggal_dibutuhkan' => $validated['tanggal_dibutuhkan'],
                'jumlah' => $jumlah,
                'ppn' => $ppn,
                'total_harga' => $totalHarga,
                'id_vendor' => $validated['id_vendor'],
                'no_faktur' => $validated['no_faktur'] ?? null,
            ]);

            foreach ($validated['details'] as $detail) {
                $kuantitas = (int) $detail['kuantitas'];
                $hargaUnit = (float) $detail['harga_unit'];
                $diskon = (float) ($detail['diskon'] ?? 0);

                $pesanan->details()->create([
                    'sku' => $detail['sku'],
                    'kuantitas' => $kuantitas,
                    'harga_unit' => $hargaUnit,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $hargaUnit) - $diskon,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pesanan-pembelian.index')
                ->with('success', 'Pesanan pembelian berhasil disimpan.');
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Pesanan pembelian gagal disimpan.');
        }
    }

    public function edit(string $pesanan_pembelian)
    {
        $pesanan = PesananPembelian::with('details')
            ->findOrFail($pesanan_pembelian);

        $vendors = MVendor::orderBy('nama_vendor')->get();
        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'pesanan-pembelian.edit',
            compact('pesanan', 'vendors', 'barangs')
        );
    }

    public function update(Request $request, string $pesanan_pembelian)
    {
        $pesanan = PesananPembelian::findOrFail($pesanan_pembelian);
        $validated = $this->validateInput($request, $pesanan);

        try {
            DB::beginTransaction();

            $jumlah = $this->calculateSubtotal($validated['details']);
            $ppn = (float) ($validated['ppn'] ?? 0);

            $pesanan->update([
                'tanggal' => $validated['tanggal'],
                'tanggal_dibutuhkan' => $validated['tanggal_dibutuhkan'],
                'jumlah' => $jumlah,
                'ppn' => $ppn,
                'total_harga' => $jumlah + $ppn,
                'id_vendor' => $validated['id_vendor'],
                'no_faktur' => $validated['no_faktur'] ?? null,
            ]);

            // Penggantian detail tetap berada dalam transaksi yang sama.
            $pesanan->details()->delete();

            foreach ($validated['details'] as $detail) {
                $kuantitas = (int) $detail['kuantitas'];
                $hargaUnit = (float) $detail['harga_unit'];
                $diskon = (float) ($detail['diskon'] ?? 0);

                $pesanan->details()->create([
                    'sku' => $detail['sku'],
                    'kuantitas' => $kuantitas,
                    'harga_unit' => $hargaUnit,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $hargaUnit) - $diskon,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pesanan-pembelian.index')
                ->with('success', 'Pesanan pembelian berhasil diperbarui.');
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Pesanan pembelian gagal diperbarui.');
        }
    }

    public function cetak(string $pesanan_pembelian)
    {
        $pesanan = PesananPembelian::with([
            'vendor',
            'details.barang',
        ])->findOrFail($pesanan_pembelian);

        return view('pesanan-pembelian.print', compact('pesanan'));
    }

    public function destroy(string $pesanan_pembelian)
    {
        $pesanan = PesananPembelian::findOrFail($pesanan_pembelian);

        try {
            DB::beginTransaction();

            $pesanan->delete();

            DB::commit();

            return redirect()
                ->route('pesanan-pembelian.index')
                ->with('success', 'Pesanan pembelian berhasil dihapus.');
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()->with(
                'error',
                'Pesanan pembelian gagal dihapus.'
            );
        }
    }

    private function validateInput(
        Request $request,
        ?PesananPembelian $pesanan = null
    ): array {
        $orderNumberRules = $pesanan
            ? [
                'required',
                'string',
                Rule::in([$pesanan->no_order_pembelian]),
            ]
            : [
                'required',
                'string',
                'max:255',
                'unique:pesanan_pembelian,no_order_pembelian',
            ];

        $validated = $request->validate([
            'no_order_pembelian' => $orderNumberRules,
            'tanggal' => ['required', 'date'],
            'tanggal_dibutuhkan' => [
                'required',
                'date',
                'after_or_equal:tanggal',
            ],
            'id_vendor' => [
                'required',
                'integer',
                'exists:m_vendor,id_vendor',
            ],
            'no_faktur' => [
                'nullable',
                'string',
                'max:255',
                'exists:permintaan_pembelian,no_faktur',
            ],
            'ppn' => ['nullable', 'numeric', 'min:0'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.sku' => [
                'required',
                'string',
                'exists:m_barang,sku',
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
        ]);

        foreach ($validated['details'] as $index => $detail) {
            $bruto = (int) $detail['kuantitas']
                * (float) $detail['harga_unit'];

            $diskon = (float) ($detail['diskon'] ?? 0);

            if ($diskon > $bruto) {
                throw ValidationException::withMessages([
                    "details.$index.diskon" =>
                        'Diskon tidak boleh melebihi nilai kuantitas dikali harga unit.',
                ]);
            }
        }

        return $validated;
    }

    private function calculateSubtotal(array $details): float
    {
        $jumlah = 0;

        foreach ($details as $detail) {
            $bruto = (int) $detail['kuantitas']
                * (float) $detail['harga_unit'];

            $diskon = (float) ($detail['diskon'] ?? 0);

            $jumlah += $bruto - $diskon;
        }

        return $jumlah;
    }
}
