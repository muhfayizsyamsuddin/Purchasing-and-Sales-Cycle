
@extends('gentelella::page')

@section('title', 'Edit Barang')
@section('page_key', 'barang')
@section('breadcrumb', 'Home > Master Data > Barang > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit Barang"
        pretitle="Master Data"
    />

    <x-gentelella::card title="Form Edit Barang">
        <form
            action="{{ route('barang.update', $barang->sku) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="sku" class="form-label">
                    SKU <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="sku"
                    name="sku"
                    value="{{ old('sku', $barang->sku) }}"
                    class="form-control @error('sku') is-invalid @enderror"
                    maxlength="255"
                    required
                >

                @error('sku')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_barang" class="form-label">
                    Nama Barang <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="nama_barang"
                    name="nama_barang"
                    value="{{ old('nama_barang', $barang->nama_barang) }}"
                    class="form-control @error('nama_barang') is-invalid @enderror"
                    maxlength="255"
                    required
                >

                @error('nama_barang')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('barang.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>
@endsection
