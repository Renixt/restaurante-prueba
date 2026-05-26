<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'current_stock',
        'minimum_stock',
        'cost',
        'supplier_id',
        'is_active',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'cost'          => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public const UNITS = [
        'kg'      => 'Kilogramos',
        'g'       => 'Gramos',
        'l'       => 'Litros',
        'ml'      => 'Mililitros',
        'unidad'  => 'Unidades',
        'porcion' => 'Porciones',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock < $this->minimum_stock;
    }
}
