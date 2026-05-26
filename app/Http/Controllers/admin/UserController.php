<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('content.users.index');
    }

    public function data()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->roles->first()?->name ?? '—',
                'edit_url'   => route('users.edit', $user),
                'delete_url' => route('users.destroy', $user),
            ];
        });

        return response()->json(['data' => $users]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name', 'name');
        return view('content.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $currentRole = $user->roles->first()?->name;
        return view('content.users.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario.'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado.']);
    }
}
