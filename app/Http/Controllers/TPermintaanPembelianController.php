<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\TPermintaanPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class TPermintaanPembelianController extends Controller
{
    public function index()
    {
        $permintaans = TPermintaanPembelian::with('details.barang')
            ->latest('no_faktur')
            ->paginate(10);

        return view(
            'permintaan-pembelian.index',
            compact('permintaans')
        );
    }

    public function create()
    {
        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'permintaan-pembelian.create',
            compact('barangs')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_faktur' => [
                'required',
                'string',
                'max:255',
                'unique:permintaan_pembelian,no_faktur',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'tanggal_diperlukan' => [
                'required',
                'date',
                'after_or_equal:tanggal',
            ],
            'description' => [
                'nullable',
                'string',
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
            'details.*.kuantitas' => [
                'required',
                'integer',
                'min:1',
            ],
            'details.*.keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::beginTransaction();

            $permintaan = TPermintaanPembelian::create([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'tanggal_diperlukan' => $validated['tanggal_diperlukan'],
                'description' => $validated['description'] ?? null,
                'status' => 'Draft',
            ]);

            foreach ($validated['details'] as $detail) {
                $permintaan->details()->create([
                    'sku' => $detail['sku'],
                    'kuantitas' => $detail['kuantitas'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('permintaan-pembelian.index')
                ->with(
                    'success',
                    'Permintaan pembelian berhasil disimpan.'
                );
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Permintaan pembelian gagal disimpan. Silakan coba lagi.'
                );
        }
    }

    public function edit(string $id)
    {
        $permintaan = TPermintaanPembelian::with('details')
            ->findOrFail($id);

        $barangs = MBarang::orderBy('nama_barang')->get();

        return view(
            'permintaan-pembelian.edit',
            compact('permintaan', 'barangs')
        );
    }

    public function update(Request $request, string $id)
    {
        $permintaan = TPermintaanPembelian::findOrFail($id);

        $validated = $request->validate([
            'no_faktur' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permintaan_pembelian', 'no_faktur')
                    ->ignore($permintaan->no_faktur, 'no_faktur'),
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'tanggal_diperlukan' => [
                'required',
                'date',
                'after_or_equal:tanggal',
            ],
            'description' => [
                'nullable',
                'string',
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
            'details.*.kuantitas' => [
                'required',
                'integer',
                'min:1',
            ],
            'details.*.keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::beginTransaction();

            $permintaan->update([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'tanggal_diperlukan' => $validated['tanggal_diperlukan'],
                'description' => $validated['description'] ?? null,
            ]);

            // Ganti detail lama dengan detail hasil validasi.
            $permintaan->details()->delete();

            foreach ($validated['details'] as $detail) {
                $permintaan->details()->create([
                    'sku' => $detail['sku'],
                    'kuantitas' => $detail['kuantitas'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('permintaan-pembelian.index')
                ->with(
                    'success',
                    'Permintaan pembelian berhasil diperbarui.'
                );
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Permintaan pembelian gagal diperbarui. Silakan coba lagi.'
                );
        }
    }

    public function destroy(string $id)
    {
        $permintaan = TPermintaanPembelian::findOrFail($id);

        try {
            DB::beginTransaction();

            $permintaan->delete();

            DB::commit();

            return redirect()
                ->route('permintaan-pembelian.index')
                ->with(
                    'success',
                    'Permintaan pembelian berhasil dihapus.'
                );
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            report($e);

            return back()->with(
                'error',
                'Permintaan pembelian gagal dihapus.'
            );
        }
    }

    public function cetak(string $id)
    {
        $permintaan = TPermintaanPembelian::with('details.barang')
            ->findOrFail($id);

        return view('permintaan-pembelian.print', compact('permintaan'));
    }
}
