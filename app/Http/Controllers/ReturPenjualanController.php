<?php

namespace App\Http\Controllers;

use App\Models\DetailMReturPenjualan;
use App\Models\PengirimanPesanan;
use App\Models\ReturPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReturPenjualanController extends Controller
{
    public function index()
    {
        $returs = ReturPenjualan::with([
            'pengirimanPesanan.pesananPenjualan',
        ])
            ->withSum('details', 'jumlah')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('retur-penjualan.index', compact('returs'));
    }

    public function create()
    {
        $pengirimans = $this->getPengirimans();

        $prefix = 'RTJ-' . now()->format('Y') . '-';

        $lastSequence = ReturPenjualan::query()
            ->where('no_retur', 'like', $prefix . '%')
            ->pluck('no_retur')
            ->map(function ($number) use ($prefix) {
                return preg_match(
                    '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                    $number,
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextNoRetur = $prefix
            . str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view(
            'retur-penjualan.create',
            compact('pengirimans', 'nextNoRetur')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules($request)
        );

        $this->validateShipmentDetails($validated);

        DB::transaction(function () use ($validated) {
            [$jumlah, $ppn, $totalHarga] =
                $this->calculateTotals($validated);

            $retur = ReturPenjualan::create([
                'no_retur' => $validated['no_retur'],
                'id_pengiriman_pesanan' =>
                    $validated['id_pengiriman_pesanan'],
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
                    'total_harga' => round(
                        ($kuantitas * $hargaUnit) - $diskon,
                        2
                    ),
                ]);
            }
        });

        return redirect()
            ->route('retur-penjualan.index')
            ->with('success', 'Retur penjualan berhasil disimpan.');
    }

    public function edit(string $no_retur)
    {
        $retur = ReturPenjualan::with('details.barang')
            ->findOrFail($no_retur);

        $pengirimans = $this->getPengirimans();

        return view(
            'retur-penjualan.edit',
            compact('retur', 'pengirimans')
        );
    }

    public function update(Request $request, string $no_retur)
    {
        $retur = ReturPenjualan::findOrFail($no_retur);

        $validated = $request->validate(
            $this->validationRules($request, $retur)
        );

        $this->validateShipmentDetails($validated, $retur);

        DB::transaction(function () use ($validated, $retur) {
            [$jumlah, $ppn, $totalHarga] =
                $this->calculateTotals($validated);

            $retur->update([
                'no_retur' => $validated['no_retur'],
                'id_pengiriman_pesanan' =>
                    $validated['id_pengiriman_pesanan'],
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
                    'total_harga' => round(
                        ($kuantitas * $hargaUnit) - $diskon,
                        2
                    ),
                ]);
            }
        });

        return redirect()
            ->route('retur-penjualan.index')
            ->with('success', 'Retur penjualan berhasil diperbarui.');
    }

    public function destroy(string $no_retur)
    {
        $retur = ReturPenjualan::findOrFail($no_retur);
        $retur->delete();

        return redirect()
            ->route('retur-penjualan.index')
            ->with('success', 'Retur penjualan berhasil dihapus.');
    }

    public function cetak(string $no_retur)
    {
        $retur = ReturPenjualan::with([
            'pengirimanPesanan.pesananPenjualan',
            'details.barang',
        ])->findOrFail($no_retur);

        return view('retur-penjualan.print', compact('retur'));
    }

    private function getPengirimans()
    {
        return PengirimanPesanan::with([
            'pesananPenjualan',
            'details.barang',
            'returPenjualan.details',
            'fakturPenjualan.details',
        ])
            ->orderByDesc('tanggal')
            ->get();
    }

    private function validationRules(
        Request $request,
        ?ReturPenjualan $retur = null
    ): array {
        $noReturRule = Rule::unique('retur_penjualan', 'no_retur');

        if ($retur !== null) {
            $noReturRule->ignore($retur->no_retur, 'no_retur');
        }

        $idPengiriman = $request->input('id_pengiriman_pesanan');

        return [
            'no_retur' => [
                'required',
                'string',
                'max:255',
                $noReturRule,
            ],
            'id_pengiriman_pesanan' => [
                'required',
                'integer',
                'exists:pengiriman_pesanan,id_pengiriman_pesanan',
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
                'required',
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
                Rule::exists(
                    'detail_m_pengiriman_pesanan',
                    'sku'
                )->where('id_pengiriman_pesanan', $idPengiriman),
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

    private function validateShipmentDetails(
        array $validated,
        ?ReturPenjualan $retur = null
    ): void {
        $pengiriman = PengirimanPesanan::with([
            'details',
            'pesananPenjualan.details',
        ])->findOrFail($validated['id_pengiriman_pesanan']);

        $barangDikirim = $pengiriman->details
            ->groupBy('sku')
            ->map(fn ($rows) => (int) $rows->sum('total'));

        foreach ($validated['details'] as $index => $detail) {
            $sku = $detail['sku'];
            $kuantitasRetur = (int) $detail['jumlah'];
            $hargaUnit = (float) $detail['harga_unit'];
            $diskon = (float) ($detail['diskon'] ?? 0);

            if (! $barangDikirim->has($sku)) {
                throw ValidationException::withMessages([
                    "details.$index.sku" =>
                        'Barang tidak terdapat dalam pengiriman yang dipilih.',
                ]);
            }
            $detailPesanan = $pengiriman->pesananPenjualan?->details
                ->firstWhere('sku', $sku);

            if ($detailPesanan === null) {
                throw ValidationException::withMessages([
                    "details.$index.sku" =>
                        'Detail barang tidak ditemukan pada Pesanan Penjualan asal.',
                ]);
            }

            // Harga unit harus sesuai dengan Pesanan Penjualan.
            $hargaAsal = (float) $detailPesanan->harga_unit;

            if (abs($hargaUnit - $hargaAsal) > 0.01) {
                throw ValidationException::withMessages([
                    "details.$index.harga_unit" =>
                        'Harga unit retur harus sesuai dengan Pesanan Penjualan asal.',
                ]);
            }

            // Diskon asal dibagi secara proporsional berdasarkan kuantitas.
            $kuantitasPesanan = max(
                (int) $detailPesanan->kuantitas,
                1
            );

            $diskonAsal = (float) ($detailPesanan->diskon ?? 0);

            $diskonSeharusnya = round(
                ($diskonAsal / $kuantitasPesanan) * $kuantitasRetur,
                2
            );

            if (abs($diskon - $diskonSeharusnya) > 0.01) {
                throw ValidationException::withMessages([
                    "details.$index.diskon" =>
                        'Diskon retur harus sesuai dengan proporsi diskon Pesanan Penjualan asal.',
                ]);
            }

            $sudahDiretur = DetailMReturPenjualan::where('sku', $sku)
                ->whereHas('returPenjualan', function ($query) use (
                    $pengiriman,
                    $retur
                ) {
                    $query->where(
                        'id_pengiriman_pesanan',
                        $pengiriman->id_pengiriman_pesanan
                    );

                    if ($retur !== null) {
                        $query->where(
                            'no_retur',
                            '!=',
                            $retur->no_retur
                        );
                    }
                })
                ->sum('jumlah');

            $kuantitasDikirim = (int) $barangDikirim->get($sku);
            $kuantitasTersedia = max(
                $kuantitasDikirim - (int) $sudahDiretur,
                0
            );

            if ($kuantitasRetur > $kuantitasTersedia) {
                throw ValidationException::withMessages([
                    "details.$index.jumlah" =>
                        "Jumlah retur melebihi sisa barang yang dapat diretur ({$kuantitasTersedia}).",
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

        $jumlah = round($jumlah, 2);

        // Ambil PPN dari faktur penjualan yang terkait dengan pengiriman.
        $pengiriman = PengirimanPesanan::with('fakturPenjualan')
            ->findOrFail($validated['id_pengiriman_pesanan']);

        $faktur = $pengiriman->fakturPenjualan;

        $fakturJumlah = (float) ($faktur?->jumlah ?? 0);
        $fakturPpn = (float) ($faktur?->ppn ?? 0);

        if ($faktur !== null && $fakturJumlah > 0) {
            $ppn = round(
                ($jumlah / $fakturJumlah) * $fakturPpn,
                2
            );
        } else {
            // Pertahankan input manual jika tidak ada faktur asal
            // atau subtotal faktur tidak dapat digunakan untuk prorata.
            $ppn = round((float) ($validated['ppn'] ?? 0), 2);
        }

        return [
            $jumlah,
            $ppn,
            round($jumlah + $ppn, 2),
        ];
    }
}