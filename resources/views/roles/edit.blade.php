
@extends('gentelella::page')

@section('title', 'Atur Permission')
@section('page_key', 'roles')
@section('breadcrumb', 'Home > Pengaturan > Role & Permission > Edit')

@section('content')
    <x-gentelella::page-header
        title="Atur Permission"
        pretitle="Role Management"
    />

    <x-gentelella::card title="Role: {{ $role->name }}">
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

        <p>
            Pilih permission yang boleh digunakan oleh role
            <strong>{{ $role->name }}</strong>.
        </p>

        <form
            action="{{ route('roles.update', $role) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            @php
                $selectedPermissions = old(
                    'permissions',
                    $rolePermissions
                );
            @endphp

            <div class="form-group">
                @foreach ($permissions as $permission)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--gt-border, #333)">
                        <label>
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                @checked(in_array($permission->name, $selectedPermissions))
                            >

                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach

                @error('permissions')
                    <div class="form-error">{{ $message }}</div>
                @enderror

                @error('permissions.*')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">
                    Simpan Permission
                </button>

                <a href="{{ route('roles.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </x-gentelella::card>
@endsection
