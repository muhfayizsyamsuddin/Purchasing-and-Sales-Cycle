<?php

namespace App\Http\Controllers;

use App\Models\DetailMPengirimanPesanan;
use App\Models\PengirimanPesanan;
use App\Models\PesananPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class PengirimanPesananController extends Controller
{
    public function index()
    {
        $pengirimans = PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
        ])
            ->withSum('details', 'total')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('pengiriman-pesanan.index', compact('pengirimans'));
    }

    public function create()
    {
        $pesanans = PesananPenjualan::with([
            'details.barang',
        ])
            ->orderByDesc('tanggal')
            ->get();
        $shippedQuantities = PengirimanPesanan::with('details')
            ->get()
            ->flatMap(function ($pengiriman) {
                return $pengiriman->details->map(function ($detail) use ($pengiriman) {
                    return [
                        'key' => $pengiriman->no_pesanan . '|' . $detail->sku,
                        'quantity' => (int) $detail->total,
                    ];
                });
            })
            ->groupBy('key')
            ->map(fn ($rows) => (int) $rows->sum('quantity'));

        return view(
            'pengiriman-pesanan.create',
            compact('pesanans', 'shippedQuantities')
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $details = $this->prepareDetails(
            $validated['details'],
            $validated['no_pesanan']
        );

        DB::transaction(function () use ($validated, $details) {
            $pengiriman = PengirimanPesanan::create([
                'no_pesanan' => $validated['no_pesanan'],
                'tanggal' => $validated['tanggal'],
                'nama_pengirim' => $validated['nama_pengirim'],
            ]);

            $pengiriman->details()->createMany($details);
        });

        return redirect()
            ->route('pengiriman-pesanan.index')
            ->with('success', 'Pengiriman pesanan berhasil ditambahkan.');
    }

    public function cetak(string $id_pengiriman_pesanan)
    {
        $pengiriman = PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
        ])->findOrFail($id_pengiriman_pesanan);

        return view('pengiriman-pesanan.print', compact('pengiriman'));
    }

    public function edit(string $id_pengiriman_pesanan)
    {
        $pengiriman = PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
        ])->findOrFail($id_pengiriman_pesanan);

        $pesanans = PesananPenjualan::with([
            'details.barang',
        ])
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'pengiriman-pesanan.edit',
            compact('pengiriman', 'pesanans')
        );
    }

    public function update(
        Request $request,
        string $id_pengiriman_pesanan
    ) {
        $pengiriman = PengirimanPesanan::findOrFail(
            $id_pengiriman_pesanan
        );

        $validated = $this->validateData($request);

        $details = $this->prepareDetails(
            $validated['details'],
            $validated['no_pesanan'],
            (int) $pengiriman->id_pengiriman_pesanan
        );

        DB::transaction(function () use (
            $pengiriman,
            $validated,
            $details
        ) {
            $pengiriman->update([
                'no_pesanan' => $validated['no_pesanan'],
                'tanggal' => $validated['tanggal'],
                'nama_pengirim' => $validated['nama_pengirim'],
            ]);

            $pengiriman->details()->delete();
            $pengiriman->details()->createMany($details);
        });

        return redirect()
            ->route('pengiriman-pesanan.index')
            ->with('success', 'Pengiriman pesanan berhasil diperbarui.');
    }

    public function destroy(string $id_pengiriman_pesanan)
    {
        $pengiriman = PengirimanPesanan::findOrFail(
            $id_pengiriman_pesanan
        );

        try {
            $pengiriman->delete();
        } catch (QueryException $e) {
            report($e);

            return redirect()
                ->route('pengiriman-pesanan.index')
                ->with(
                    'error',
                    'Pengiriman tidak dapat dihapus karena masih digunakan oleh Faktur Penjualan atau Retur Penjualan.'
                );
        }

        return redirect()
            ->route('pengiriman-pesanan.index')
            ->with('success', 'Pengiriman pesanan berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'no_pesanan' => [
                'required',
                'string',
                'exists:pesanan_penjualan,no_pesanan',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'nama_pengirim' => [
                'required',
                'string',
                'max:255',
            ],
            'details' => [
                'required',
                'array',
                'min:1',
            ],
            'details.*.sku' => [
                'required',
                'string',
                'exists:m_barang,sku',
            ],
            'details.*.keterangan' => [
                'nullable',
                'string',
            ],
            'details.*.total' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);
    }

    private function prepareDetails(
        array $details,
        string $noPesanan,
        ?int $exceptId = null
    ): array {
        $pesanan = PesananPenjualan::with('details')
            ->findOrFail($noPesanan);

        // Total kuantitas yang dipesan untuk setiap SKU.
        $ordered = $pesanan->details
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('kuantitas'));

        // Pengiriman lain untuk pesanan yang sama.
        $shipmentQuery = PengirimanPesanan::where(
            'no_pesanan',
            $noPesanan
        );

        if ($exceptId !== null) {
            $shipmentQuery->where(
                'id_pengiriman_pesanan',
                '!=',
                $exceptId
            );
        }

        $shipmentIds = $shipmentQuery
            ->pluck('id_pengiriman_pesanan');

        $shipped = DetailMPengirimanPesanan::whereIn(
            'id_pengiriman_pesanan',
            $shipmentIds
        )
            ->get()
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('total'));

        // Jumlah yang akan disimpan untuk setiap SKU.
        $requested = collect($details)
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('total'));

        foreach ($requested as $sku => $quantity) {
            if (!isset($ordered[$sku])) {
                throw ValidationException::withMessages([
                    'details' => "Barang {$sku} tidak terdapat dalam pesanan penjualan.",
                ]);
            }

            $remaining = $ordered[$sku] - ($shipped[$sku] ?? 0);

            if ($quantity > $remaining) {
                throw ValidationException::withMessages([
                    'details' => "Jumlah pengiriman barang {$sku} melebihi sisa kuantitas pesanan ({$remaining}).",
                ]);
            }
        }

        return collect($details)
            ->map(fn ($detail) => [
                'sku' => $detail['sku'],
                'keterangan' => $detail['keterangan'] ?? null,
                'total' => (int) $detail['total'],
            ])
            ->values()
            ->all();
    }
}