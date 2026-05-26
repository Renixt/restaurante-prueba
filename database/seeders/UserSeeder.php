<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrador SGR', 'email' => 'admin@sgr.local',   'role' => 'administrador'],
            ['name' => 'Gerente SGR',        'email' => 'gerente@sgr.local', 'role' => 'gerente'],
            ['name' => 'Mesero Uno',         'email' => 'mesero@sgr.local',  'role' => 'mesero'],
            ['name' => 'Cocinero Uno',       'email' => 'cocina@sgr.local',  'role' => 'cocinero'],
            ['name' => 'Almacén SGR',        'email' => 'almacen@sgr.local', 'role' => 'almacen'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
