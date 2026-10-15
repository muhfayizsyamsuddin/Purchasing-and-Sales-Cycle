@extends('gentelella::page')

@section('title', 'Role & Permission')
@section('page_key', 'roles')
@section('breadcrumb', 'Home > Pengaturan > Role & Permission')

@section('content')
    <div class="roles-page">
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

            <style>
                .roles-table {
                    width: 100% !important;
                    table-layout: fixed !important;
                }

                .roles-table th,
                .roles-table td {
                    vertical-align: middle;
                }

                .roles-table th:nth-child(1),
                .roles-table td:nth-child(1) {
                    width: 16%;
                    text-align: center;
                }

                .roles-table th:nth-child(2),
                .roles-table td:nth-child(2) {
                    width: 16%;
                    text-align: center;
                }

                .roles-table th:nth-child(3),
                .roles-table td:nth-child(3) {
                    width: 48%;
                    text-align: left;
                    overflow-wrap: anywhere;
                }

                .roles-table th:nth-child(4),
                .roles-table td:nth-child(4) {
                    width: 20%;
                    text-align: center;
                }
            </style>

            <x-gentelella::table
                :datatable="true"
                :page-length="10"
                class="roles-table"
                style="width:100%; table-layout:fixed;"
            >
                <colgroup>
                    <col style="width:16%">
                    <col style="width:16%">
                    <col style="width:48%">
                    <col style="width:20%">
                </colgroup>

                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Jumlah Permission</th>
                        <th>Permission</th>
                        <th data-orderable="false">Aksi</th>
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
                            <td colspan="4" style="text-align:center; padding:32px 16px;">
                                <div style="font-weight:600; margin-bottom:4px;">
                                    Belum ada role
                                </div>
                                <div style="color:var(--text-muted); font-size:13px;">
                                    Data role akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-gentelella::table>
        </x-gentelella::card>
    </div>
@endsection