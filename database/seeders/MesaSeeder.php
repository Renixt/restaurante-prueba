<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesas = [
            ['numero' => '1', 'capacidad' => 2],
            ['numero' => '2', 'capacidad' => 4],
            ['numero' => '3', 'capacidad' => 4],
            ['numero' => '4', 'capacidad' => 6],
            ['numero' => '5', 'capacidad' => 6],
            ['numero' => '6', 'capacidad' => 8],
            ['numero' => '7', 'capacidad' => 2],
            ['numero' => '8', 'capacidad' => 4],
            ['numero' => 'Terraza 1', 'capacidad' => 4],
            ['numero' => 'Terraza 2', 'capacidad' => 6],
        ];

        foreach ($mesas as $mesa) {
            Mesa::firstOrCreate(['numero' => $mesa['numero']], $mesa + ['activa' => true]);
        }
    }
}
