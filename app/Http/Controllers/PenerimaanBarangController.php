<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\PesananPembelian;
use App\Models\PenerimaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        $penerimaans = PenerimaanBarang::with('pesanan.vendor')
            ->withSum('details', 'kuantitas')
            ->orderByDesc('tanggal')
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

        // Hitung total kuantitas yang sudah diterima per pesanan dan SKU.
        $receivedQuantities = DB::table('penerimaan_barang as p')
            ->join(
                'detail_m_penerimaan_barang as d',
                'd.no_penerimaan',
                '=',
                'p.no_penerimaan'
            )
            ->select(
                'p.no_order_pembelian',
                'd.sku',
                DB::raw('SUM(d.kuantitas) as received_quantity')
            )
            ->groupBy('p.no_order_pembelian', 'd.sku')
            ->get()
            ->keyBy(fn ($row) => $row->no_order_pembelian . '|' . $row->sku);

        // Kirim hanya barang yang masih memiliki sisa untuk diterima.
        $orderOptions = $pesanans->map(function ($pesanan) use ($receivedQuantities) {
            $items = $pesanan->details->map(function ($detail) use (
                $pesanan,
                $receivedQuantities
            ) {
                $key = $pesanan->no_order_pembelian . '|' . $detail->sku;

                $sudahDiterima = (int) (
                    $receivedQuantities->get($key)->received_quantity ?? 0
                );

                $sisa = max(
                    (int) $detail->kuantitas - $sudahDiterima,
                    0
                );

                return [
                    'sku' => $detail->sku,
                    'label' => $detail->sku . ' — '
                        . ($detail->barang?->nama_barang ?? 'Barang tidak ditemukan'),
                    'kuantitas' => $sisa,
                ];
            })
                ->filter(fn ($item) => $item['kuantitas'] > 0)
                ->values();

            return [
                'value' => $pesanan->no_order_pembelian,
                'label' => $pesanan->no_order_pembelian
                    . ' — '
                    . ($pesanan->vendor?->nama_vendor ?? 'Tanpa vendor'),
                'items' => $items->all(),
            ];
        })
            ->filter(fn ($order) => count($order['items']) > 0)
            ->values()
            ->all();

        $barangs = MBarang::orderBy('nama_barang')->get();

        // Buat nomor penerimaan berikutnya, misalnya PB-2026-003.
        $prefix = 'PB-' . now()->format('Y') . '-';

        $lastSequence = PenerimaanBarang::query()
            ->where('no_penerimaan', 'like', $prefix . '%')
            ->selectRaw(
                'MAX(CAST(SUBSTRING(no_penerimaan, 9) AS UNSIGNED)) as max_sequence'
            )
            ->value('max_sequence');

        $nextNoPenerimaan = $prefix
            . str_pad((string) ((int) $lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view(
            'penerimaan-barang.create',
            compact('pesanans', 'barangs', 'orderOptions', 'nextNoPenerimaan')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules($request)
        );
        $this->validateAvailableQuantities($validated);

        try {
            DB::transaction(function () use ($validated) {
                $penerimaan = PenerimaanBarang::create([
                    'no_penerimaan' => $validated['no_penerimaan'],
                    'tanggal' => $validated['tanggal'],
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
        $this->validateAvailableQuantities($validated, $penerimaan);

        try {
            DB::transaction(function () use ($penerimaan, $validated) {
                $penerimaan->update([
                    'no_penerimaan' => $validated['no_penerimaan'],
                    'tanggal' => $validated['tanggal'],
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

    private function validateAvailableQuantities(
        array $validated,
        ?PenerimaanBarang $existing = null
    ): void {
        $noOrder = $validated['no_order_pembelian'];

        foreach ($validated['details'] as $index => $detail) {
            $sku = $detail['sku'];

            $orderedQuantity = (int) DB::table('detail_m_pesanan_pembelian')
                ->where('no_order_pembelian', $noOrder)
                ->where('sku', $sku)
                ->value('kuantitas');

            $receivedQuery = DB::table('penerimaan_barang as p')
                ->join(
                    'detail_m_penerimaan_barang as d',
                    'd.no_penerimaan',
                    '=',
                    'p.no_penerimaan'
                )
                ->where('p.no_order_pembelian', $noOrder)
                ->where('d.sku', $sku);

            // Saat edit, penerimaan yang sedang diedit tidak dihitung
            // sebagai penerimaan lain.
            if ($existing !== null) {
                $receivedQuery->where(
                    'p.no_penerimaan',
                    '!=',
                    $existing->no_penerimaan
                );
            }

            $alreadyReceived = (int) $receivedQuery->sum('d.kuantitas');

            $remainingQuantity = max(
                $orderedQuantity - $alreadyReceived,
                0
            );

            if ((int) $detail['kuantitas'] > $remainingQuantity) {
                throw ValidationException::withMessages([
                    "details.$index.kuantitas" =>
                        "Kuantitas penerimaan SKU {$sku} melebihi sisa pesanan. "
                        . "Maksimal yang dapat diterima: {$remainingQuantity} unit.",
                ]);
            }
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
            'tanggal' => [
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
