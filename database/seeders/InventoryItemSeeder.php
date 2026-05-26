<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $carnes   = Supplier::where('rfc', 'DCS990101AAA')->first();
        $verduras = Supplier::where('rfc', 'VFF010203BBB')->first();
        $costena  = Supplier::where('rfc', 'PLC850515CCC')->first();

        $items = [
            ['name' => 'Carne de res',     'sku' => 'RES-001', 'unit' => 'kg',      'current_stock' => 10.000, 'minimum_stock' => 3.000, 'cost' => 180.00, 'supplier_id' => $carnes?->id],
            ['name' => 'Pollo entero',     'sku' => 'POL-001', 'unit' => 'kg',      'current_stock' => 8.000,  'minimum_stock' => 2.000, 'cost' => 80.00,  'supplier_id' => $carnes?->id],
            ['name' => 'Lechuga romana',   'sku' => 'LEC-001', 'unit' => 'kg',      'current_stock' => 3.000,  'minimum_stock' => 1.000, 'cost' => 25.00,  'supplier_id' => $verduras?->id],
            ['name' => 'Tomate bola',      'sku' => 'TOM-001', 'unit' => 'kg',      'current_stock' => 5.000,  'minimum_stock' => 2.000, 'cost' => 20.00,  'supplier_id' => $verduras?->id],
            ['name' => 'Tortilla de maíz', 'sku' => 'TOR-001', 'unit' => 'unidad',  'current_stock' => 200.000,'minimum_stock' => 50.000,'cost' => 1.50,   'supplier_id' => $costena?->id],
            ['name' => 'Aceite vegetal',   'sku' => 'ACE-001', 'unit' => 'l',       'current_stock' => 6.000,  'minimum_stock' => 2.000, 'cost' => 35.00,  'supplier_id' => $costena?->id],
            ['name' => 'Sal de mesa',      'sku' => 'SAL-001', 'unit' => 'g',       'current_stock' => 1000.000,'minimum_stock' => 200.000,'cost' => 0.01, 'supplier_id' => $costena?->id],
            ['name' => 'Queso Oaxaca',     'sku' => 'QUE-001', 'unit' => 'kg',      'current_stock' => 4.000,  'minimum_stock' => 1.000, 'cost' => 150.00, 'supplier_id' => $carnes?->id],
            ['name' => 'Cebolla blanca',   'sku' => 'CEB-001', 'unit' => 'kg',      'current_stock' => 4.000,  'minimum_stock' => 1.000, 'cost' => 15.00,  'supplier_id' => $verduras?->id],
            ['name' => 'Chile serrano',    'sku' => 'CHI-001', 'unit' => 'g',       'current_stock' => 500.000,'minimum_stock' => 100.000,'cost' => 0.05,  'supplier_id' => $verduras?->id],
        ];

        foreach ($items as $data) {
            $data['is_active'] = true;
            InventoryItem::firstOrCreate(['sku' => $data['sku']], $data);
        }
    }
}
