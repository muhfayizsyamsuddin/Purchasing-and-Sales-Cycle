<?php

namespace App\Http\Controllers;

use App\Models\FakturPenjualan;
use App\Models\PembayaranPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembayaranPenjualanController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPenjualan::with('fakturPenjualan')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view(
            'pembayaran-penjualan.index',
            compact('pembayarans')
        );
    }

    public function create()
    {
        $fakturs = FakturPenjualan::with('pembayaran')
            ->orderByDesc('tanggal')
            ->get()
            ->map(function ($faktur) {
                $terbayar = (float) $faktur->pembayaran->sum('jumlah');

                $faktur->jumlah_terbayar = $terbayar;
                $faktur->sisa_tagihan = max(
                    (float) $faktur->total_harga - $terbayar,
                    0
                );

                return $faktur;
            })
            ->filter(fn ($faktur) => $faktur->sisa_tagihan > 0)
            ->values();
        $prefix = 'PBAY-PJ-' . now()->format('Y') . '-';

        $lastSequence = PembayaranPenjualan::query()
            ->where('no_pembayaran', 'like', $prefix . '%')
            ->pluck('no_pembayaran')
            ->map(function ($number) use ($prefix) {
                return preg_match(
                    '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                    $number,
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextNoPembayaran = $prefix
            . str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view(
            'pembayaran-penjualan.create',
            compact('fakturs', 'nextNoPembayaran')
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated) {
            $faktur = FakturPenjualan::where(
                'no_faktur',
                $validated['no_faktur']
            )
                ->lockForUpdate()
                ->firstOrFail();

            $terbayar = (float) PembayaranPenjualan::where(
                'no_faktur',
                $faktur->no_faktur
            )->sum('jumlah');

            $sisaTagihan = max(
                (float) $faktur->total_harga - $terbayar,
                0
            );

            if ((float) $validated['jumlah'] > $sisaTagihan) {
                throw ValidationException::withMessages([
                    'jumlah' => sprintf(
                        'Pembayaran melebihi sisa tagihan sebesar Rp%s.',
                        number_format($sisaTagihan, 2, ',', '.')
                    ),
                ]);
            }

            PembayaranPenjualan::create($validated);
        });

        return redirect()
            ->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran penjualan berhasil ditambahkan.');
    }

    public function edit(string $no_pembayaran)
    {
        $pembayaran = PembayaranPenjualan::with('fakturPenjualan')
            ->findOrFail($no_pembayaran);

        $fakturs = FakturPenjualan::with('pembayaran')
            ->orderByDesc('tanggal')
            ->get()
            ->map(function ($faktur) use ($pembayaran) {
                $terbayar = (float) $faktur->pembayaran
                    ->where('no_pembayaran', '!=', $pembayaran->no_pembayaran)
                    ->sum('jumlah');

                $faktur->jumlah_terbayar = $terbayar;
                $faktur->sisa_tagihan = max(
                    (float) $faktur->total_harga - $terbayar,
                    0
                );

                return $faktur;
            })
            ->filter(fn ($faktur) =>
                $faktur->sisa_tagihan > 0
                || $faktur->no_faktur === $pembayaran->no_faktur
            )
            ->values();

        return view(
            'pembayaran-penjualan.edit',
            compact('pembayaran', 'fakturs')
        );
    }

    public function update(
        Request $request,
        string $no_pembayaran
    ) {
        $pembayaran = PembayaranPenjualan::findOrFail($no_pembayaran);

        $validated = $this->validateData($request, $pembayaran);

        DB::transaction(function () use ($validated, $pembayaran) {
            $faktur = FakturPenjualan::where(
                'no_faktur',
                $validated['no_faktur']
            )
                ->lockForUpdate()
                ->firstOrFail();

            $terbayarLain = (float) PembayaranPenjualan::where(
                'no_faktur',
                $faktur->no_faktur
            )
                ->where('no_pembayaran', '!=', $pembayaran->no_pembayaran)
                ->sum('jumlah');

            $sisaTagihan = max(
                (float) $faktur->total_harga - $terbayarLain,
                0
            );

            if ((float) $validated['jumlah'] > $sisaTagihan) {
                throw ValidationException::withMessages([
                    'jumlah' => sprintf(
                        'Pembayaran melebihi sisa tagihan sebesar Rp%s.',
                        number_format($sisaTagihan, 2, ',', '.')
                    ),
                ]);
            }

            $pembayaran->update($validated);
        });

        return redirect()
            ->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran penjualan berhasil diperbarui.');
    }

    public function destroy(string $no_pembayaran)
    {
        $pembayaran = PembayaranPenjualan::findOrFail($no_pembayaran);
        $pembayaran->delete();

        return redirect()
            ->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran penjualan berhasil dihapus.');
    }

    public function cetak(string $no_pembayaran)
    {
        $pembayaran = PembayaranPenjualan::with('fakturPenjualan')
            ->findOrFail($no_pembayaran);

        return view('pembayaran-penjualan.print', compact('pembayaran'));
    }

    private function validateData(
        Request $request,
        ?PembayaranPenjualan $pembayaran = null
    ): array {
        return $request->validate([
            'no_pembayaran' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pembayaran_penjualan', 'no_pembayaran')
                    ->ignore($pembayaran?->no_pembayaran, 'no_pembayaran'),
            ],
            'tanggal' => ['required', 'date'],
            'terima_dari' => ['required', 'string', 'max:255'],
            'terbilang' => ['required', 'string'],
            'untuk_pembayaran' => ['required', 'string'],
            'jumlah' => ['required', 'numeric', 'gt:0'],
            'no_faktur' => [
                'required',
                'string',
                'exists:faktur_penjualan,no_faktur',
            ],
        ]);
    }
}