<?php

namespace App\Http\Controllers\inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Supplier;
use App\Services\InventoryService;

class InventoryItemController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        $lowStockCount = InventoryItem::where('is_active', true)
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->count();

        return view('content.inventory.index', compact('lowStockCount'));
    }

    public function data()
    {
        $items = InventoryItem::with('supplier')->get()->map(function ($item) {
            return [
                'id'            => $item->id,
                'name'          => $item->name,
                'sku'           => $item->sku,
                'unit'          => $item->unit,
                'unit_label'    => InventoryItem::UNITS[$item->unit] ?? $item->unit,
                'current_stock' => (float) $item->current_stock,
                'minimum_stock' => (float) $item->minimum_stock,
                'cost'          => number_format($item->cost, 2),
                'supplier'      => $item->supplier?->business_name ?? '—',
                'is_active'     => $item->is_active,
                'low_stock'     => $item->isLowStock(),
            ];
        });

        return response()->json(['data' => $items]);
    }

    public function create()
    {
        $units     = InventoryItem::UNITS;
        $suppliers = Supplier::where('status', 'activo')->orderBy('business_name')->get();
        return view('content.inventory.create', compact('units', 'suppliers'));
    }

    public function store(StoreInventoryItemRequest $request)
    {
        InventoryItem::create($request->validated());
        return redirect()->route('inventory.index')->with('success', 'Insumo registrado correctamente.');
    }

    public function edit(InventoryItem $inventory)
    {
        $units     = InventoryItem::UNITS;
        $suppliers = Supplier::where('status', 'activo')->orderBy('business_name')->get();
        return view('content.inventory.edit', compact('inventory', 'units', 'suppliers'));
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventory)
    {
        $inventory->update($request->validated());
        return redirect()->route('inventory.index')->with('success', 'Insumo actualizado correctamente.');
    }

    public function destroy(InventoryItem $inventory)
    {
        if ($inventory->recipes()->exists() || $inventory->purchaseOrderItems()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un insumo con recetas o pedidos asociados.'
            ], 403);
        }

        $inventory->delete();
        return response()->json(['message' => 'Insumo eliminado.']);
    }

    public function movements(InventoryItem $inventory)
    {
        $movements = $inventory->movements()
            ->with('creator')
            ->latest()
            ->paginate(30);

        return view('content.inventory.movements', compact('inventory', 'movements'));
    }
}
