<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\PesananPembelian;
use App\Models\PenerimaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        $penerimaans = PenerimaanBarang::with('pesanan.vendor')
            ->withCount('details')
            ->orderByDesc('tanggal_penerimaan')
            ->paginate(10);

        return view(
            'penerimaan-barang.index',
            compact('penerimaans')
        );
    }

    public function create()
    {
        $pesanans = PesananPembelian::with([
            'vendor',
            'details.barang',
        ])
            ->orderBy('no_order_pembelian')
            ->get();

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'penerimaan-barang.create',
            compact('pesanans', 'barangs')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules($request)
        );

        try {
            DB::transaction(function () use ($validated) {
                $penerimaan = PenerimaanBarang::create([
                    'no_penerimaan' => $validated['no_penerimaan'],
                    'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                    'no_order_pembelian' => $validated['no_order_pembelian'],
                ]);

                foreach ($validated['details'] as $detail) {
                    $penerimaan->details()->create([
                        'sku' => $detail['sku'],
                        'kuantitas' => $detail['kuantitas'],
                        'kondisi_barang' => $detail['kondisi_barang'],
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                }
            });

            return redirect()
                ->route('penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil disimpan.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Penerimaan barang gagal disimpan. Silakan coba lagi.'
                );
        }
    }

    public function cetak(string $no_penerimaan)
    {
        $penerimaan = PenerimaanBarang::with([
            'pesanan.vendor',
            'details.barang',
        ])->findOrFail($no_penerimaan);

        return view(
            'penerimaan-barang.print',
            compact('penerimaan')
        );
    }

    public function edit(string $no_penerimaan)
    {
        $penerimaan = PenerimaanBarang::with('details')
            ->findOrFail($no_penerimaan);

        $pesanans = PesananPembelian::with([
            'vendor',
            'details.barang',
        ])
            ->orderBy('no_order_pembelian')
            ->get();

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'penerimaan-barang.edit',
            compact('penerimaan', 'pesanans', 'barangs')
        );
    }

    public function update(Request $request, string $no_penerimaan)
    {
        $penerimaan = PenerimaanBarang::findOrFail($no_penerimaan);

        $validated = $request->validate(
            $this->validationRules($request, $penerimaan)
        );

        try {
            DB::transaction(function () use ($penerimaan, $validated) {
                $penerimaan->update([
                    'no_penerimaan' => $validated['no_penerimaan'],
                    'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                    'no_order_pembelian' => $validated['no_order_pembelian'],
                ]);

                // Ganti detail lama dengan detail yang sudah divalidasi.
                $penerimaan->details()->delete();

                foreach ($validated['details'] as $detail) {
                    $penerimaan->details()->create([
                        'sku' => $detail['sku'],
                        'kuantitas' => $detail['kuantitas'],
                        'kondisi_barang' => $detail['kondisi_barang'],
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                }
            });

            return redirect()
                ->route('penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Penerimaan barang gagal diperbarui. Silakan coba lagi.'
                );
        }
    }

    public function destroy(string $no_penerimaan)
    {
        $penerimaan = PenerimaanBarang::findOrFail($no_penerimaan);

        try {
            DB::transaction(function () use ($penerimaan) {
                $penerimaan->delete();
            });

            return redirect()
                ->route('penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Penerimaan barang gagal dihapus.'
            );
        }
    }

    private function validationRules(
        Request $request,
        ?PenerimaanBarang $existing = null
    ): array {
        $uniqueNoPenerimaan = Rule::unique(
            'penerimaan_barang',
            'no_penerimaan'
        );

        if ($existing !== null) {
            $uniqueNoPenerimaan->ignore(
                $existing->no_penerimaan,
                'no_penerimaan'
            );
        }

        $noOrder = $request->input('no_order_pembelian');

        return [
            'no_penerimaan' => [
                'required',
                'string',
                'max:255',
                $uniqueNoPenerimaan,
            ],
            'tanggal_penerimaan' => [
                'required',
                'date',
            ],
            'no_order_pembelian' => [
                'required',
                'string',
                'exists:pesanan_pembelian,no_order_pembelian',
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
                Rule::exists('detail_m_pesanan_pembelian', 'sku')
                    ->where('no_order_pembelian', $noOrder),
            ],
            'details.*.kuantitas' => [
                'required',
                'integer',
                'min:1',
            ],
            'details.*.kondisi_barang' => [
                'required',
                'string',
                'max:255',
            ],
            'details.*.keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }
}
