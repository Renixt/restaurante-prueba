<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'business_name' => 'Distribuidora Carnes La Superior S.A. de C.V.',
                'rfc'           => 'DCS990101AAA',
                'phone'         => '5512345678',
                'email'         => 'ventas@carneslasuperior.com',
                'address'       => 'Av. Central 100, Col. Industrial, CDMX',
                'status'        => 'activo',
            ],
            [
                'business_name' => 'Verduras y Frutas Fresh S.A. de C.V.',
                'rfc'           => 'VFF010203BBB',
                'phone'         => '5598765432',
                'email'         => 'pedidos@verdurafresh.mx',
                'address'       => 'Mercado Central Local 45, CDMX',
                'status'        => 'activo',
            ],
            [
                'business_name' => 'Productos La Costeña S.A. de C.V.',
                'rfc'           => 'PLC850515CCC',
                'phone'         => '5534567890',
                'email'         => 'pedidos@lacostena.com.mx',
                'address'       => 'Blvd. Toluca 200, Estado de México',
                'status'        => 'activo',
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(['rfc' => $data['rfc']], $data);
        }
    }
}
