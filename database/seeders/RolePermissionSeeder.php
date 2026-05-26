<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Menú
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
            // Órdenes
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
            'orders.status', 'orders.cocina',
            // Mesas
            'mesas.view', 'mesas.create', 'mesas.edit', 'mesas.delete',
            // Inventario
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'recipes.view', 'recipes.edit',
            // Proveedores
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit',
            'purchase-orders.delete', 'purchase-orders.status',
            // Usuarios
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Roles
            'roles.view', 'roles.edit',
            // Reportes
            'reports.view', 'reports.export',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Administrador — acceso total
        $admin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Gerente
        $gerente = Role::firstOrCreate(['name' => 'gerente', 'guard_name' => 'web']);
        $gerente->syncPermissions([
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.status', 'orders.cocina',
            'mesas.view', 'mesas.create', 'mesas.edit', 'mesas.delete',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'recipes.view', 'recipes.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit',
            'purchase-orders.delete', 'purchase-orders.status',
            'reports.view', 'reports.export',
        ]);

        // Mesero
        $mesero = Role::firstOrCreate(['name' => 'mesero', 'guard_name' => 'web']);
        $mesero->syncPermissions([
            'menu.view',
            'orders.view', 'orders.create', 'orders.edit', 'orders.status',
            'mesas.view',
        ]);

        // Cocinero
        $cocinero = Role::firstOrCreate(['name' => 'cocinero', 'guard_name' => 'web']);
        $cocinero->syncPermissions([
            'orders.view', 'orders.status', 'orders.cocina',
            'menu.view',
        ]);

        // Almacén
        $almacen = Role::firstOrCreate(['name' => 'almacen', 'guard_name' => 'web']);
        $almacen->syncPermissions([
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'recipes.view', 'recipes.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit',
            'purchase-orders.delete', 'purchase-orders.status',
        ]);
    }
}
