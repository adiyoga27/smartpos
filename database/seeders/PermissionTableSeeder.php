<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard' => ['view'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'brands' => ['view', 'create', 'edit', 'delete'],
            'units' => ['view', 'create', 'edit', 'delete'],
            'tax-rates' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'contacts' => ['view', 'create', 'edit', 'delete'],
            'customer-groups' => ['view', 'create', 'edit', 'delete'],
            'pos' => ['view', 'create'],
            'sales' => ['view', 'delete'],
            'purchases' => ['view', 'create', 'edit', 'delete'],
            'stock' => ['view', 'create'],
            'expenses' => ['view', 'create', 'edit', 'delete'],
            'expense-categories' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view'],
            'cash-register' => ['view', 'create'],
            'accounts' => ['view', 'create', 'edit', 'delete'],
            'account-types' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();

        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
