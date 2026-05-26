<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'status',
        'subtotal',
        'total',
        'delivery_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'total'         => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public const STATUS_TRANSITIONS = [
        'pendiente' => ['enviado', 'cancelado'],
        'enviado'   => ['recibido', 'cancelado'],
        'recibido'  => [],
        'cancelado' => [],
    ];

    public const STATUS_LABELS = [
        'pendiente' => ['label' => 'Pendiente', 'class' => 'bg-label-warning'],
        'enviado'   => ['label' => 'Enviado',   'class' => 'bg-label-info'],
        'recibido'  => ['label' => 'Recibido',  'class' => 'bg-label-success'],
        'cancelado' => ['label' => 'Cancelado', 'class' => 'bg-label-secondary'],
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum('total');
        $this->update(['subtotal' => $subtotal, 'total' => $subtotal]);
    }

    public function canReceive(): bool
    {
        return $this->status === 'enviado' && $this->items()->exists();
    }

    public function canEdit(): bool
    {
        return $this->status === 'pendiente';
    }
}
