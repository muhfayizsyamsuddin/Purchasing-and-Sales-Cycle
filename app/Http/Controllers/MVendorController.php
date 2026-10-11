<?php

namespace App\Http\Controllers;

use App\Models\MVendor;
use Illuminate\Http\Request;

class MVendorController extends Controller
{
    public function index()
    {
        $vendors = MVendor::orderBy('id_vendor')->paginate(10);

        return view('vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        MVendor::create($validated);

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Data vendor berhasil ditambahkan.');
    }

    public function edit(int $vendor)
    {
        $vendor = MVendor::findOrFail($vendor);

        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, int $vendor)
    {
        $vendor = MVendor::findOrFail($vendor);

        $validated = $request->validate([
            'nama_vendor' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $vendor->update($validated);

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Data vendor berhasil diperbarui.');
    }

    public function destroy(int $vendor)
    {
        $vendor = MVendor::findOrFail($vendor);
        $vendor->delete();

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Data vendor berhasil dihapus.');
    }
}