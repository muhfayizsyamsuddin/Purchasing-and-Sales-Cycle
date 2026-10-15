
@extends('gentelella::page')

@section('title', 'Tambah Barang')
@section('page_key', 'barang')
@section('breadcrumb', 'Home > Master Data > Barang > Tambah')

@section('content')
    <div class="barang-form-page">
        <x-gentelella::page-header
            title="Tambah Barang"
            pretitle="Master Data"
        />

        <style>
            .barang-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .barang-form-grid .form-group {
                min-width: 0;
                margin-bottom: 0;
            }

            .barang-form-page .form-control {
                width: 100%;
                box-sizing: border-box;
            }

            .barang-form-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid var(--border-color);
            }

            @media (max-width: 700px) {
                .barang-form-grid {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 16px;
                }

                .barang-form-actions {
                    flex-wrap: wrap;
                }
            }
        </style>

        <x-gentelella::card title="Informasi Barang">
            <p style="color:var(--text-muted); font-size:13px; margin:0 0 24px;">
                Masukkan SKU dan nama barang untuk menambahkan data inventaris.
                Kolom bertanda <span class="required">*</span> wajib diisi.
            </p>

            <form action="{{ route('barang.store') }}" method="POST">
                @csrf

                <div class="barang-form-grid">
                    <div class="form-group">
                        <label for="sku" class="form-label">
                            SKU <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            value="{{ old('sku') }}"
                            class="form-control @error('sku') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Masukkan SKU barang"
                            required
                            autofocus
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
                            value="{{ old('nama_barang') }}"
                            class="form-control @error('nama_barang') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Masukkan nama barang"
                            required
                        >

                        @error('nama_barang')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="barang-form-actions">
                    <a href="{{ route('barang.index') }}" class="btn btn-outline">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Barang
                    </button>
                </div>
            </form>
        </x-gentelella::card>
    </div>
@endsection
