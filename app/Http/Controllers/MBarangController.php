<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class MBarangController extends Controller
{
    public function index()
    {
        $barangs = MBarang::orderBy('sku')->paginate(10);

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $prefix = 'BRG-';

        $lastSequence = MBarang::query()
            ->where('sku', 'like', $prefix . '%')
            ->pluck('sku')
            ->map(function ($sku) use ($prefix) {
                return preg_match(
                    '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                    $sku,
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextSku = $prefix
            . str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return view('barang.create', compact('nextSku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                'unique:m_barang,sku',
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        MBarang::create($validated);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function edit(string $sku)
    {
        $barang = MBarang::findOrFail($sku);

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, string $sku)
    {
        $barang = MBarang::findOrFail($sku);

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('m_barang', 'sku')
                    ->ignore($barang->sku, 'sku'),
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $barang->update($validated);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $sku)
    {
        $barang = MBarang::findOrFail($sku);

        try {
            $barang->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('barang.index')
                    ->with(
                        'error',
                        'Barang tidak dapat dihapus karena masih digunakan dalam transaksi.'
                    );
            }

            throw $e;
        }

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}