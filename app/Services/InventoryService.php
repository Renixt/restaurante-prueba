<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Recipe;

class InventoryService
{
    /**
     * Verifica si hay suficiente inventario para preparar una orden.
     * Retorna ['ok' => bool, 'missing' => [['name', 'needed', 'available', 'unit']]]
     */
    public function canFulfillOrder(Order $order): array
    {
        $order->loadMissing('items');
        $needed = $this->aggregateNeeded($order);

        $missing = [];
        foreach ($needed as $itemId => $quantity) {
            $inventoryItem = InventoryItem::find($itemId);
            if (!$inventoryItem) continue;
            if ($inventoryItem->current_stock < $quantity) {
                $missing[] = [
                    'name'      => $inventoryItem->name,
                    'needed'    => round($quantity, 3),
                    'available' => round((float) $inventoryItem->current_stock, 3),
                    'unit'      => $inventoryItem->unit,
                ];
            }
        }

        return ['ok' => empty($missing), 'missing' => $missing];
    }

    /**
     * Descuenta insumos del inventario al preparar una orden.
     * Debe llamarse dentro de una transacción de BD.
     */
    public function deductForOrder(Order $order, ?int $userId = null): void
    {
        $order->loadMissing('items');
        $needed = $this->aggregateNeeded($order);

        foreach ($needed as $itemId => $quantity) {
            $item = InventoryItem::lockForUpdate()->find($itemId);
            if (!$item) continue;

            $before = (float) $item->current_stock;
            $after  = max(0, $before - $quantity);

            $item->update(['current_stock' => $after]);

            InventoryMovement::create([
                'inventory_item_id' => $itemId,
                'type'              => 'salida',
                'quantity'          => $quantity,
                'before_stock'      => $before,
                'after_stock'       => $after,
                'reason'            => 'Preparación orden #' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'reference_type'    => 'order',
                'reference_id'      => $order->id,
                'created_by'        => $userId,
            ]);
        }
    }

    /**
     * Agrega stock al recibir una orden de compra.
     * Debe llamarse dentro de una transacción de BD.
     */
    public function receiveFromPurchaseOrder(PurchaseOrder $po, ?int $userId = null): void
    {
        $po->loadMissing('items');

        foreach ($po->items as $poItem) {
            $item = InventoryItem::lockForUpdate()->findOrFail($poItem->inventory_item_id);

            $before = (float) $item->current_stock;
            $after  = $before + (float) $poItem->quantity;

            $item->update(['current_stock' => $after]);

            InventoryMovement::create([
                'inventory_item_id' => $poItem->inventory_item_id,
                'type'              => 'entrada',
                'quantity'          => $poItem->quantity,
                'before_stock'      => $before,
                'after_stock'       => $after,
                'reason'            => 'Pedido #' . str_pad($po->id, 5, '0', STR_PAD_LEFT),
                'reference_type'    => 'purchase_order',
                'reference_id'      => $po->id,
                'created_by'        => $userId,
            ]);
        }
    }

    /**
     * Retorna insumos con stock por debajo del mínimo.
     */
    public function getLowStockItems()
    {
        return InventoryItem::where('is_active', true)
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->with('supplier')
            ->orderBy('name')
            ->get();
    }

    /**
     * Agrega las cantidades necesarias de insumos para todos los items de una orden.
     * Retorna [inventory_item_id => total_quantity_needed]
     */
    private function aggregateNeeded(Order $order): array
    {
        $needed = [];

        foreach ($order->items as $orderItem) {
            $recipes = Recipe::where('menu_item_id', $orderItem->menu_item_id)->get();
            foreach ($recipes as $recipe) {
                $id = $recipe->inventory_item_id;
                $needed[$id] = ($needed[$id] ?? 0) + ((float) $recipe->quantity_required * $orderItem->quantity);
            }
        }

        return $needed;
    }
}
