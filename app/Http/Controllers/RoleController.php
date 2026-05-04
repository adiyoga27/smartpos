<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->paginate(20);
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'other';
        });

        $permissionModules = $permissions->map(function ($items, $module) {
            return [
                'name' => $module,
                'permissions' => $items->pluck('name')->toArray(),
            ];
        })->values()->toArray();

        return view('role.index', compact('roles', 'permissionModules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        if ($data['permissions'] ?? false) {
            $role->syncPermissions($data['permissions']);
        }

        return back()->with('success', 'Role berhasil dibuat.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update(['name' => $data['name']]);

        if ($data['permissions'] ?? false) {
            $role->syncPermissions($data['permissions']);
        }

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['Admin'])) {
            return back()->with('error', 'Role Admin tidak dapat dihapus.');
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}
