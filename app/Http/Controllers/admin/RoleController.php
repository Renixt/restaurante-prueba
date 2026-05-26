<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('name')->get();
        return view('content.roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->pluck('name');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('content.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Role $role)
    {
        $permissions = request()->input('permissions', []);
        $role->syncPermissions($permissions);
        return redirect()->route('roles.index')->with('success', "Permisos del rol '{$role->name}' actualizados.");
    }
}
