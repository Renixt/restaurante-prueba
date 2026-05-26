<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $table = 'mesas';

    protected $fillable = ['numero', 'capacidad', 'activa', 'estado', 'ubicacion'];

    protected $casts = ['activa' => 'boolean'];

    public const ESTADOS = [
        'disponible' => ['label' => 'Disponible', 'class' => 'bg-label-success'],
        'ocupada'    => ['label' => 'Ocupada',    'class' => 'bg-label-danger'],
        'reservada'  => ['label' => 'Reservada',  'class' => 'bg-label-warning'],
        'limpieza'   => ['label' => 'En limpieza','class' => 'bg-label-info'],
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isDisponible(): bool
    {
        return $this->activa && $this->estado === 'disponible';
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? $this->estado;
    }

    public function getEstadoClassAttribute(): string
    {
        return self::ESTADOS[$this->estado]['class'] ?? 'bg-label-secondary';
    }
}
