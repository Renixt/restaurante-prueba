<?php

namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        return view('content.purchase-orders.index');
    }

    public function data()
    {
        $orders = PurchaseOrder::with('supplier')->latest()->get()->map(function ($po) {
            $info = PurchaseOrder::STATUS_LABELS[$po->status];
            return [
                'id'            => $po->id,
                'folio'         => '#' . str_pad($po->id, 5, '0', STR_PAD_LEFT),
                'supplier'      => $po->supplier?->business_name ?? '—',
                'status'        => $po->status,
                'status_label'  => $info['label'],
                'status_class'  => $info['class'],
                'total'         => number_format($po->total, 2),
                'delivery_date' => $po->delivery_date?->format('d/m/Y') ?? '—',
                'created_at'    => $po->created_at->format('d/m/Y'),
                'can_edit'      => $po->canEdit(),
            ];
        });

        return response()->json(['data' => $orders]);
    }

    public function create()
    {
        $suppliers      = Supplier::where('status', 'activo')->orderBy('business_name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();
        return view('content.purchase-orders.create', compact('suppliers', 'inventoryItems'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'supplier_id'   => $data['supplier_id'],
                'status'        => 'pendiente',
                'delivery_date' => $data['delivery_date'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_cost'];
                $po->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity'          => $item['quantity'],
                    'unit_cost'         => $item['unit_cost'],
                    'total'             => $lineTotal,
                ]);
            }

            $po->recalculateTotal();
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Pedido creado correctamente.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.inventoryItem', 'creator']);
        return view('content.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canEdit()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Solo se pueden editar pedidos en estado pendiente.');
        }

        $purchaseOrder->load(['supplier', 'items.inventoryItem']);
        $suppliers      = Supplier::where('status', 'activo')->orderBy('business_name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('content.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'inventoryItems'));
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canEdit()) {
            return back()->with('error', 'Solo se pueden editar pedidos en estado pendiente.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $purchaseOrder) {
            $purchaseOrder->items()->delete();

            $purchaseOrder->update([
                'supplier_id'   => $data['supplier_id'],
                'delivery_date' => $data['delivery_date'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_cost'];
                $purchaseOrder->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity'          => $item['quantity'],
                    'unit_cost'         => $item['unit_cost'],
                    'total'             => $lineTotal,
                ]);
            }

            $purchaseOrder->recalculateTotal();
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['pendiente', 'cancelado'])) {
            return response()->json([
                'message' => 'Solo se pueden eliminar pedidos pendientes o cancelados.'
            ], 403);
        }

        $purchaseOrder->delete();
        return response()->json(['message' => 'Pedido eliminado.']);
    }

    public function updateStatus(PurchaseOrder $purchaseOrder, string $newStatus)
    {
        $allowed = PurchaseOrder::STATUS_TRANSITIONS[$purchaseOrder->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return back()->with('error', 'Cambio de estado no permitido.');
        }

        if ($newStatus === 'recibido' && !$purchaseOrder->items()->exists()) {
            return back()->with('error', 'El pedido no tiene insumos registrados.');
        }

        DB::transaction(function () use ($purchaseOrder, $newStatus) {
            $purchaseOrder->update(['status' => $newStatus]);

            if ($newStatus === 'recibido') {
                $this->inventoryService->receiveFromPurchaseOrder($purchaseOrder, auth()->id());
            }
        });

        $label = PurchaseOrder::STATUS_LABELS[$newStatus]['label'];
        return back()->with('success', "Pedido marcado como: {$label}.");
    }
}
