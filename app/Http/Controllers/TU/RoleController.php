<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('tu.roles.index', compact('roles', 'permissions'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('tu.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Prevent editing TU role permissions (always full access)
        if ($role->name === 'TU') {
            return back()->with('error', 'Role TU tidak dapat diubah permission-nya.');
        }

        $role->syncPermissions($validated['permissions'] ?? []);

        // Clear Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('tu.roles.index')
            ->with('success', "Permission untuk role {$role->name} berhasil diperbarui.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('tu.roles.index')
            ->with('success', "Role {$role->name} berhasil dibuat.");
    }

    public function destroy(Role $role)
    {
        // Prevent deleting default roles
        if (in_array($role->name, ['TU', 'Guru', 'Kepsek'])) {
            return back()->with('error', 'Role default tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->route('tu.roles.index')
            ->with('success', "Role berhasil dihapus.");
    }
}
