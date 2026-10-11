
@extends('gentelella::page')

@section('title', 'Role & Permission')
@section('page_key', 'roles')
@section('breadcrumb', 'Home > Pengaturan > Role & Permission')

@section('content')
    <x-gentelella::page-header
        title="Role & Permission"
        pretitle="Pengaturan"
    />

    <x-gentelella::card title="Daftar Role">
        @if (session('success'))
            <div class="banner banner-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="banner banner-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Jumlah Permission</th>
                    <th>Permission</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>

                        <td>{{ $role->permissions->count() }}</td>

                        <td>
                            {{ $role->permissions->pluck('name')->implode(', ') ?: '-' }}
                        </td>

                        <td>
                            @if ($role->name === 'Admin')
                                <span>Protected</span>
                            @else
                                <a
                                    href="{{ route('roles.edit', $role) }}"
                                    class="btn btn-sm btn-outline"
                                >
                                    Atur Permission
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada role.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>
    </x-gentelella::card>
@endsection
