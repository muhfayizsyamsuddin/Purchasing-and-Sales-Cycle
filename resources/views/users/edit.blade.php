@extends('gentelella::page')

@section('title', 'Edit User')
@section('page_key', 'users')
@section('breadcrumb', 'Home > Pengaturan > User Management > Edit')

@section('content')
    <x-gentelella::page-header
        title="Edit User"
        pretitle="Pengaturan"
    />

    <x-gentelella::card title="Form Edit User">
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

        @php
            $selectedRoles = old(
                'roles',
                $user->roles->pluck('name')->all()
            );
        @endphp

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
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
                    value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid @enderror"
                    maxlength="255"
                    required
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    Password Baru
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    minlength="8"
                    autocomplete="new-password"
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
                                @checked(in_array($role->name, $selectedRoles))
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
                    Simpan Perubahan
                </button>

                <a href="{{ route('users.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>
@endsection