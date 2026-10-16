
@extends('gentelella::page')

@section('title', 'User Management')
@section('page_key', 'users')
@section('breadcrumb', 'Home > Pengaturan > User Management')

@section('content')
    <div class="users-page">
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

            <style>
                .users-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .users-table th,
                .users-table td {
                    text-align: center !important;
                    vertical-align: middle;
                }

                .users-table td:last-child {
                    white-space: nowrap;
                }

                .users-table td:nth-child(2) {
                    overflow-wrap: anywhere;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="users-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:25%">
                    <col style="width:35%">
                    <col style="width:20%">
                    <col style="width:20%">
                </colgroup>

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th data-orderable="false">Aksi</th>
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
                                        >Edit</a>
                                    @endcan
                                @endcan

                                @can('users.delete')
                                    <form
                                        action="{{ route('users.destroy', $user) }}"
                                        method="POST"
                                        style="display:inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline"
                                        >Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada user
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    User yang ditambahkan akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>

            <div style="margin-top:16px;">
                {{ $users->links() }}
            </div>
        </x-gentelella::card>
    </div>
@endsection