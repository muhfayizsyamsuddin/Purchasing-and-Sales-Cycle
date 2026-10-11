<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MBarangController extends Controller
{
    public function index()
    {
        $barangs = MBarang::orderBy('sku')->paginate(10);

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
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
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}