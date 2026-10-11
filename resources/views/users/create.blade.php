@extends('gentelella::page')

@section('title', 'Tambah User')
@section('page_key', 'users')
@section('breadcrumb', 'Home > Pengaturan > User Management > Tambah')

@section('content')
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

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    maxlength="255"
                    required
                >
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    maxlength="255"
                    required
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password
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

            <div class="form-group">
                <label class="form-label">Role</label>

                @foreach ($roles as $role)
                    <div>
                        <label>
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                @checked(in_array($role->name, old('roles', [])))
                            >
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach

                @error('roles')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('users.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>
@endsection