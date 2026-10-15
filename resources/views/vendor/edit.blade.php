
@extends('gentelella::page')

@section('title', 'Edit Vendor')
@section('page_key', 'vendor')
@section('breadcrumb', 'Home > Master Data > Vendor > Edit')

@section('content')
    <div class="vendor-form-page">
        <x-gentelella::page-header
            title="Edit Vendor"
            pretitle="Master Data"
        />

        <style>
            .vendor-form-page .vendor-form-field {
                max-width: 640px;
            }

            .vendor-form-page .form-control {
                width: 100%;
                box-sizing: border-box;
            }

            .vendor-form-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }

            @media (max-width: 640px) {
                .vendor-form-actions {
                    flex-wrap: wrap;
                }
            }
        </style>

        <x-gentelella::card title="Informasi Vendor">
            <p style="color:var(--text-muted); font-size:13px; margin:0 0 24px;">
                Perbarui nama vendor atau pemasok sesuai kebutuhan.
                Kolom bertanda <span class="required">*</span> wajib diisi.
            </p>

            <form
                action="{{ route('vendor.update', $vendor->id_vendor) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="vendor-form-field">
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
                            autofocus
                        >

                        @error('nama_vendor')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="vendor-form-actions">
                    <a href="{{ route('vendor.index') }}" class="btn btn-outline">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-gentelella::card>
    </div>
@endsection
