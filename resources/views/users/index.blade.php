@extends('gentelella::page')

@section('title', 'User Management')
@section('page_key', 'users')
@section('breadcrumb', 'Home > Pengaturan > User Management')

@section('content')
    <x-gentelella::page-header
        title="User Management"
        pretitle="Pengaturan"
    >
        <x-slot:actions>
            @can('users.create')
                @can('roles.manage')
                    <x-gentelella::btn
                        :href="route('users.create')"
                        variant="primary"
                    >
                        Tambah User
                    </x-gentelella::btn>
                @endcan
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Daftar User">
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
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{ $user->roles->pluck('name')->implode(', ') ?: '-' }}
                        </td>
                        <td>
                            @can('users.update')
                                @can('roles.manage')
                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Edit
                                    </a>
                                @endcan
                            @endcan

                            @can('users.delete')
                                <form
                                    action="{{ route('users.destroy', $user) }}"
                                    method="POST"
                                    style="display: inline"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        <div style="margin-top: 16px">
            {{ $users->links() }}
        </div>
    </x-gentelella::card>
@endsection