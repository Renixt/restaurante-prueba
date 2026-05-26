<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'mesa_id',
        'type',
        'status',
        'payment_method',
        'subtotal',
        'total',
        'split_count',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal'    => 'decimal:2',
        'total'       => 'decimal:2',
        'split_count' => 'integer',
    ];

    public const STATUS_TRANSITIONS = [
        'pendiente'      => 'en_preparacion',
        'en_preparacion' => 'listo',
        'listo'          => 'entregado',
        'entregado'      => 'pagado',
        'pagado'         => null,
    ];

    public const STATUS_LABELS = [
        'pendiente'      => ['label' => 'Pendiente',       'class' => 'bg-label-warning'],
        'en_preparacion' => ['label' => 'En preparación',  'class' => 'bg-label-info'],
        'listo'          => ['label' => 'Listo',           'class' => 'bg-label-success'],
        'entregado'      => ['label' => 'Entregado',       'class' => 'bg-label-primary'],
        'pagado'         => ['label' => 'Pagado',          'class' => 'bg-label-secondary'],
    ];

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNextStatus(): ?string
    {
        return self::STATUS_TRANSITIONS[$this->status] ?? null;
    }

    public function canBeDeleted(): bool
    {
        return $this->status !== 'pagado';
    }

    public function canModifyItems(): bool
    {
        return $this->status === 'pendiente';
    }

    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum('total');
        $this->update(['subtotal' => $subtotal, 'total' => $subtotal]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['label'] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['class'] ?? 'bg-label-secondary';
    }

    public function getPerPersonAttribute(): string
    {
        $count = max(1, $this->split_count);
        return number_format($this->total / $count, 2);
    }
}
