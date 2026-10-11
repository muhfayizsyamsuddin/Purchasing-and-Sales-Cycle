<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        // Role Admin dilindungi agar akses administrator tidak terkunci.
        if ($role->name === 'Admin') {
            return redirect()
                ->route('roles.index')
                ->with(
                    'error',
                    'Permission role Admin dilindungi dan tidak dapat diubah.'
                );
        }

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $rolePermissions = $role->permissions
            ->pluck('name')
            ->all();

        return view(
            'roles.edit',
            compact('role', 'permissions', 'rolePermissions')
        );
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'Admin') {
            return redirect()
                ->route('roles.index')
                ->with(
                    'error',
                    'Permission role Admin dilindungi dan tidak dapat diubah.'
                );
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'required',
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);

        DB::transaction(function () use ($role, $validated) {
            $role->syncPermissions($validated['permissions'] ?? []);
        });

        return redirect()
            ->route('roles.index')
            ->with(
                'success',
                'Permission role ' . $role->name . ' berhasil diperbarui.'
            );
    }
}
