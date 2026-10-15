
@extends('gentelella::page')

@section('title', 'Tambah User')
@section('page_key', 'users')
@section('breadcrumb', 'Home > Pengaturan > User Management > Tambah')

@section('content')
    <style>
        .users-form-page .form-section {
            margin-bottom: 24px;
        }

        .users-form-page .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }

        .users-form-page .form-group {
            min-width: 0;
            margin-bottom: 0;
        }

        .users-form-page .form-group > .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .users-form-page .form-control {
            width: 100%;
            min-width: 0;
        }

        .users-form-page .required {
            color: #dc3545;
        }

        .users-form-page .form-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 13px;
        }

        .users-form-page .section-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .users-form-page .section-description {
            margin: 0 0 18px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .users-form-page .role-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .users-form-page .role-option {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 160px;
            padding: 12px 16px;
            border: 1px solid #344256;
            border-radius: 6px;
            cursor: pointer;
        }

        .users-form-page .role-option input {
            margin: 0;
        }

        .users-form-page .role-option span {
            overflow-wrap: anywhere;
        }

        .users-form-page .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #344256;
        }

        @media (max-width: 767px) {
            .users-form-page .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .users-form-page .role-option {
                flex: 1 1 100%;
            }
        }
    </style>

    <div class="users-form-page">
        <x-gentelella::page-header
            title="Tambah User"
            pretitle="Pengaturan"
        />

        <x-gentelella::card title="Form Tambah User">
            @if ($errors->any())
                <div class="banner banner-danger" role="alert">
                    <strong>Periksa kembali input berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('users.store') }}"
                method="POST"
                id="user-create-form"
            >
                @csrf

                <div class="form-section">
                    <h3 class="section-heading">Informasi Akun</h3>
                    <p class="section-description">
                        Isi informasi dasar untuk membuat akun pengguna baru.
                    </p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nama <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                maxlength="255"
                                autocomplete="name"
                                required
                            >

                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                maxlength="255"
                                autocomplete="email"
                                required
                            >

                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                Password <span class="required">*</span>
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <p class="section-description" style="margin: 7px 0 0;">
                                Minimal 8 karakter.
                            </p>

                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password <span class="required">*</span>
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-section">
                    <h3 class="section-heading">Hak Akses</h3>
                    <p class="section-description">
                        Pilih role yang akan diberikan kepada pengguna.
                    </p>

                    <div class="role-list">
                        @forelse ($roles as $role)
                            <label class="role-option">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->name }}"
                                    @checked(in_array($role->name, old('roles', [])))
                                >
                                <span>{{ $role->name }}</span>
                            </label>
                        @empty
                            <p class="section-description">
                                Belum ada role yang tersedia.
                            </p>
                        @endforelse
                    </div>

                    @error('roles')
                        <div class="form-error">{{ $message }}</div>
                    @enderror

                    @error('roles.*')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('users.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan User
                    </button>
                </div>
            </form>
        </x-gentelella::card>
    </div>
@endsection
