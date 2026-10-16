<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\PesananPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PesananPenjualanController extends Controller
{
    public function index()
    {
        $pesanans = PesananPenjualan::with('details.barang')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('pesanan-penjualan.index', compact('pesanans'));
    }

    public function create()
    {
        $barangs = MBarang::orderBy('nama_barang')->get();

        $prefix = 'PJ-' . now()->format('Y') . '-';

        $lastSequence = PesananPenjualan::query()
            ->where('no_pesanan', 'like', $prefix . '%')
            ->pluck('no_pesanan')
            ->map(function ($number) use ($prefix) {
                return preg_match(
                    '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                    $number,
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextNoPesanan = $prefix
            . str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view(
            'pesanan-penjualan.create',
            compact('barangs', 'nextNoPesanan')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pesanan' => [
                'required',
                'string',
                'max:255',
                'unique:pesanan_penjualan,no_pesanan',
            ],
            'tanggal' => ['required', 'date'],
            'nama_pembeli' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'ppn' => ['required', 'numeric', 'min:0'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.sku' => ['required', 'exists:m_barang,sku'],
            'details.*.kuantitas' => ['required', 'integer', 'min:1'],
            'details.*.harga_unit' => ['required', 'numeric', 'min:0'],
            'details.*.diskon' => ['required', 'numeric', 'min:0'],
        ]);

        $details = $this->prepareDetails($validated['details']);
        $subtotal = collect($details)->sum('total_harga');
        $ppn = (float) $validated['ppn'];

        DB::transaction(function () use (
            $validated,
            $details,
            $subtotal,
            $ppn
        ) {
            $pesanan = PesananPenjualan::create([
                'no_pesanan' => $validated['no_pesanan'],
                'tanggal' => $validated['tanggal'],
                'nama_pembeli' => $validated['nama_pembeli'],
                'alamat' => $validated['alamat'],
                'jumlah' => $subtotal,
                'ppn' => $ppn,
                'total_harga' => $subtotal + $ppn,
            ]);

            $pesanan->details()->createMany($details);
        });

        return redirect()
            ->route('pesanan-penjualan.index')
            ->with('success', 'Pesanan penjualan berhasil ditambahkan.');
    }

    public function show(string $no_pesanan)
    {
        $pesanan = PesananPenjualan::with('details.barang')
            ->findOrFail($no_pesanan);

        return view('pesanan-penjualan.show', compact('pesanan'));
    }

    public function cetak(string $no_pesanan)
    {
        $pesanan = PesananPenjualan::with('details.barang')
            ->findOrFail($no_pesanan);

        return view('pesanan-penjualan.print', compact('pesanan'));
    }

    public function edit(string $no_pesanan)
    {
        $pesanan = PesananPenjualan::with('details.barang')
            ->findOrFail($no_pesanan);

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'pesanan-penjualan.edit',
            compact('pesanan', 'barangs')
        );
    }

    public function update(Request $request, string $no_pesanan)
    {
        $pesanan = PesananPenjualan::findOrFail($no_pesanan);

        $validated = $request->validate([
            'no_pesanan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pesanan_penjualan', 'no_pesanan')
                    ->ignore($pesanan->no_pesanan, 'no_pesanan'),
            ],
            'tanggal' => ['required', 'date'],
            'nama_pembeli' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'ppn' => ['required', 'numeric', 'min:0'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.sku' => ['required', 'exists:m_barang,sku'],
            'details.*.kuantitas' => ['required', 'integer', 'min:1'],
            'details.*.harga_unit' => ['required', 'numeric', 'min:0'],
            'details.*.diskon' => ['required', 'numeric', 'min:0'],
        ]);

        $details = $this->prepareDetails($validated['details']);
        $subtotal = collect($details)->sum('total_harga');
        $ppn = (float) $validated['ppn'];

        DB::transaction(function () use (
            $pesanan,
            $validated,
            $details,
            $subtotal,
            $ppn
        ) {
            $pesanan->update([
                'no_pesanan' => $validated['no_pesanan'],
                'tanggal' => $validated['tanggal'],
                'nama_pembeli' => $validated['nama_pembeli'],
                'alamat' => $validated['alamat'],
                'jumlah' => $subtotal,
                'ppn' => $ppn,
                'total_harga' => $subtotal + $ppn,
            ]);

            $pesanan->details()->delete();
            $pesanan->details()->createMany($details);
        });

        return redirect()
            ->route('pesanan-penjualan.index')
            ->with('success', 'Pesanan penjualan berhasil diperbarui.');
    }

    public function destroy(string $no_pesanan)
    {
        $pesanan = PesananPenjualan::findOrFail($no_pesanan);
        $pesanan->delete();

        return redirect()
            ->route('pesanan-penjualan.index')
            ->with('success', 'Pesanan penjualan berhasil dihapus.');
    }

    private function prepareDetails(array $details): array
    {
        foreach ($details as $index => &$detail) {
            $bruto = (float) $detail['kuantitas']
                * (float) $detail['harga_unit'];

            $diskon = (float) $detail['diskon'];

            if ($diskon > $bruto) {
                throw ValidationException::withMessages([
                    "details.$index.diskon" =>
                        'Diskon tidak boleh melebihi harga total barang.',
                ]);
            }

            $detail = [
                'sku' => $detail['sku'],
                'kuantitas' => (int) $detail['kuantitas'],
                'harga_unit' => (float) $detail['harga_unit'],
                'diskon' => $diskon,
                'total_harga' => round($bruto - $diskon, 2),
            ];
        }

        unset($detail);

        return $details;
    }
}