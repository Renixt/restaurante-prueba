<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'categoria',
        'imagen',
        'disponible',
    ];

    protected $casts = [
        'precio'     => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public static array $categorias = [
        'Entradas',
        'Sopas',
        'Ensaladas',
        'Platos Fuertes',
        'Postres',
        'Bebidas',
        'Especiales',
    ];
}
