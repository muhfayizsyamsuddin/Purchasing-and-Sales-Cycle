
@extends('gentelella::page')

@section('title', 'Atur Permission')
@section('page_key', 'roles')
@section('breadcrumb', 'Home > Pengaturan > Role & Permission > Edit')

@section('content')
    @php
        $selectedPermissions = old(
            'permissions',
            $rolePermissions
        );
    @endphp

    <style>
        .role-permission-page .permission-description {
            margin: 0 0 22px;
            color: #a9b8ca;
            line-height: 1.7;
        }

        .role-permission-page .role-name {
            color: #ffffff;
            font-weight: 700;
        }

        .role-permission-page .permission-heading {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
        }

        .role-permission-page .permission-subheading {
            margin: 0 0 18px;
            color: #a9b8ca;
            font-size: 13px;
        }

        .role-permission-page .permission-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .role-permission-page .permission-option {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            padding: 14px 16px;
            border: 1px solid #344256;
            border-radius: 6px;
            cursor: pointer;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }

        .role-permission-page .permission-option:hover {
            border-color: #526782;
            background-color: rgba(148, 163, 184, 0.06);
        }

        .role-permission-page .permission-option input {
            flex-shrink: 0;
            margin: 0;
        }

        .role-permission-page .permission-name {
            min-width: 0;
            overflow-wrap: anywhere;
            color: #f1f5f9;
            font-weight: 500;
        }

        .role-permission-page .form-error {
            margin-top: 8px;
            color: #dc3545;
            font-size: 13px;
        }

        .role-permission-page .permission-empty {
            padding: 18px;
            border: 1px dashed #344256;
            border-radius: 6px;
            color: #a9b8ca;
        }

        .role-permission-page .form-actions {
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
            .role-permission-page .permission-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="role-permission-page">
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

            @if (session('error'))
                <div class="banner banner-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <p class="permission-description">
                Pilih permission yang boleh digunakan oleh role
                <span class="role-name">{{ $role->name }}</span>.
            </p>

            <form
                action="{{ route('roles.update', $role) }}"
                method="POST"
                id="role-permission-form"
            >
                @csrf
                @method('PUT')

                <div>
                    <h3 class="permission-heading">Daftar Permission</h3>
                    <p class="permission-subheading">
                        Centang permission yang ingin diberikan kepada role ini.
                    </p>

                    @forelse ($permissions as $permission)
                        @if ($loop->first)
                            <div class="permission-grid">
                        @endif

                        <label class="permission-option">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                @checked(in_array($permission->name, $selectedPermissions))
                            >
                            <span class="permission-name">
                                {{ $permission->name }}
                            </span>
                        </label>

                        @if ($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="permission-empty">
                            Belum ada permission yang tersedia.
                        </div>
                    @endforelse

                    @error('permissions')
                        <div class="form-error">{{ $message }}</div>
                    @enderror

                    @error('permissions.*')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('roles.index') }}"
                        class="btn btn-outline"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Permission
                    </button>
                </div>
            </form>
        </x-gentelella::card>
    </div>
@endsection
