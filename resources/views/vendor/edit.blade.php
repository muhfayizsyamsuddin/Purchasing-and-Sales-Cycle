
@extends('gentelella::page')

@section('title', 'Edit Vendor')
@section('page_key', 'vendor')
@section('breadcrumb', 'Home > Master Data > Vendor > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit Vendor"
        pretitle="Master Data"
    />

    <x-gentelella::card title="Form Edit Vendor">
        <form
            action="{{ route('vendor.update', $vendor->id_vendor) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_vendor" class="form-label">
                    Nama Vendor <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="nama_vendor"
                    name="nama_vendor"
                    value="{{ old('nama_vendor', $vendor->nama_vendor) }}"
                    class="form-control @error('nama_vendor') is-invalid @enderror"
                    maxlength="255"
                    required
                >

                @error('nama_vendor')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('vendor.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>
@endsection
