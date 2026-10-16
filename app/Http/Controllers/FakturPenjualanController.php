<?php

namespace App\Http\Controllers;

use App\Models\DetailMFakturPenjualan;
use App\Models\FakturPenjualan;
use App\Models\PengirimanPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FakturPenjualanController extends Controller
{
    public function index()
    {
        $fakturs = FakturPenjualan::with([
            'pengirimanPesanan.pesananPenjualan',
        ])
            ->withCount('details')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('faktur-penjualan.index', compact('fakturs'));
    }

    public function create()
    {
        $pengirimans = PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
        ])
            ->whereDoesntHave('fakturPenjualan')
            ->orderByDesc('tanggal')
            ->get();
        $prefix = 'FJ-' . now()->format('Y') . '-';

        $lastSequence = FakturPenjualan::query()
            ->where('no_faktur', 'like', $prefix . '%')
            ->pluck('no_faktur')
            ->map(function ($number) use ($prefix) {
                return preg_match(
                    '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                    $number,
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextNoFaktur = $prefix
            . str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view(
            'faktur-penjualan.create',
            compact('pengirimans', 'nextNoFaktur')
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $details = $this->prepareDetails(
            $validated['details'],
            (int) $validated['id_pengiriman_pesanan']
        );

        $jumlah = collect($details)->sum('total_harga');
        $ppn = (float) $validated['ppn'];

        DB::transaction(function () use (
            $validated,
            $details,
            $jumlah,
            $ppn
        ) {
            $faktur = FakturPenjualan::create([
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
                'id_pengiriman_pesanan' =>
                    $validated['id_pengiriman_pesanan'],
            ]);

            $faktur->details()->createMany($details);
        });

        return redirect()
            ->route('faktur-penjualan.index')
            ->with('success', 'Faktur penjualan berhasil ditambahkan.');
    }

    public function edit(string $no_faktur)
    {
        $faktur = FakturPenjualan::with('details.barang')
            ->findOrFail($no_faktur);

        $pengirimans = PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
        ])
            ->where(function ($query) use ($faktur) {
                $query->whereDoesntHave('fakturPenjualan')
                    ->orWhere(
                        'id_pengiriman_pesanan',
                        $faktur->id_pengiriman_pesanan
                    );
            })
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'faktur-penjualan.edit',
            compact('faktur', 'pengirimans')
        );
    }

    public function update(Request $request, string $no_faktur)
    {
        $faktur = FakturPenjualan::findOrFail($no_faktur);

        $validated = $this->validateData($request, $faktur);

        $details = $this->prepareDetails(
            $validated['details'],
            (int) $validated['id_pengiriman_pesanan']
        );

        $jumlah = collect($details)->sum('total_harga');
        $ppn = (float) $validated['ppn'];

        DB::transaction(function () use (
            $faktur,
            $validated,
            $details,
            $jumlah,
            $ppn
        ) {
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
                'id_pengiriman_pesanan' =>
                    $validated['id_pengiriman_pesanan'],
            ]);

            $faktur->details()->delete();
            $faktur->details()->createMany($details);
        });

        return redirect()
            ->route('faktur-penjualan.index')
            ->with('success', 'Faktur penjualan berhasil diperbarui.');
    }

    public function destroy(string $no_faktur)
    {
        $faktur = FakturPenjualan::findOrFail($no_faktur);
        $faktur->delete();

        return redirect()
            ->route('faktur-penjualan.index')
            ->with('success', 'Faktur penjualan berhasil dihapus.');
    }

    public function cetak(string $no_faktur)
    {
        $faktur = FakturPenjualan::with([
            'pengirimanPesanan.pesananPenjualan',
            'details.barang',
        ])->findOrFail($no_faktur);

        return view('faktur-penjualan.print', compact('faktur'));
    }

    private function validateData(
        Request $request,
        ?FakturPenjualan $faktur = null
    ): array {
        return $request->validate([
            'no_faktur' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faktur_penjualan', 'no_faktur')
                    ->ignore($faktur?->no_faktur, 'no_faktur'),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jangka_waktu_pembayaran' => [
                'required',
                'string',
                'max:255',
            ],
            'ppn' => ['required', 'numeric', 'min:0'],
            'id_pengiriman_pesanan' => [
                'required',
                'integer',
                'exists:pengiriman_pesanan,id_pengiriman_pesanan',
                Rule::unique(
                    'faktur_penjualan',
                    'id_pengiriman_pesanan'
                )->ignore($faktur?->no_faktur, 'no_faktur'),
            ],
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
                'required',
                'numeric',
                'min:0',
            ],
        ]);
    }

    private function prepareDetails(
        array $details,
        int $idPengirimanPesanan
    ): array {
        $pengiriman = PengirimanPesanan::with('details')
            ->findOrFail($idPengirimanPesanan);

        $available = $pengiriman->details
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('total'));

        $requested = collect($details)
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('kuantitas'));

        foreach ($requested as $sku => $quantity) {
            if (!isset($available[$sku])) {
                throw ValidationException::withMessages([
                    'details' =>
                        "Barang {$sku} tidak terdapat dalam pengiriman yang dipilih.",
                ]);
            }

            if ($quantity > $available[$sku]) {
                throw ValidationException::withMessages([
                    'details' =>
                        "Kuantitas faktur barang {$sku} melebihi jumlah yang dikirim ({$available[$sku]}).",
                ]);
            }
        }

        return collect($details)
            ->map(function ($detail, $index) {
                $bruto = (float) $detail['kuantitas']
                    * (float) $detail['harga_unit'];

                $diskon = (float) $detail['diskon'];

                if ($diskon > $bruto) {
                    throw ValidationException::withMessages([
                        "details.{$index}.diskon" =>
                            'Diskon tidak boleh melebihi harga total barang.',
                    ]);
                }

                return [
                    'sku' => $detail['sku'],
                    'kuantitas' => (int) $detail['kuantitas'],
                    'harga_unit' => (float) $detail['harga_unit'],
                    'diskon' => $diskon,
                    'total_harga' => round($bruto - $diskon, 2),
                ];
            })
            ->values()
            ->all();
    }
}