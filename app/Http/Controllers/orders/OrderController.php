<?php

namespace App\Http\Controllers\orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Mesa;
use App\Models\MenuItem;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        return view('content.orders.index');
    }

    public function data(Request $request)
    {
        $orders = Order::with(['mesa'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $statusInfo = Order::STATUS_LABELS[$order->status] ?? ['label' => $order->status, 'class' => ''];
                return [
                    'id'             => $order->id,
                    'folio'          => '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'mesa'           => $order->type === 'mesa'
                        ? ($order->mesa ? 'Mesa ' . $order->mesa->numero : 'Mesa ?')
                        : 'Para llevar',
                    'type'           => $order->type,
                    'status'         => $order->status,
                    'status_label'   => $statusInfo['label'],
                    'status_class'   => $statusInfo['class'],
                    'total'          => number_format($order->total, 2),
                    'payment_method' => $order->payment_method ?? '—',
                    'created_at'     => $order->created_at->format('d/m/Y H:i'),
                    'can_delete'     => $order->canBeDeleted(),
                    'next_status'    => $order->getNextStatus(),
                ];
            });

        return response()->json(['data' => $orders]);
    }

    public function create()
    {
        $mesas     = Mesa::where('activa', true)->where('estado', 'disponible')->orderBy('numero')->get();
        $menuItems = MenuItem::where('disponible', true)->orderBy('categoria')->orderBy('nombre')->get();

        return view('content.orders.create', compact('mesas', 'menuItems'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $mesaId = $data['type'] === 'mesa' ? ($data['mesa_id'] ?? null) : null;

            $order = Order::create([
                'mesa_id'        => $mesaId,
                'type'           => $data['type'],
                'status'         => 'pendiente',
                'payment_method' => $data['payment_method'] ?? null,
                'split_count'    => $data['split_count'] ?? 1,
                'notes'          => $data['notes'] ?? null,
                'created_by'     => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $lineTotal = $menuItem->precio * $item['quantity'];
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $menuItem->precio,
                    'total'        => $lineTotal,
                ]);
            }

            $order->recalculateTotal();

            // Marcar la mesa como ocupada
            if ($mesaId) {
                Mesa::where('id', $mesaId)->update(['estado' => 'ocupada']);
            }
        });

        return redirect()->route('orders.index')
            ->with('success', 'Orden creada correctamente.');
    }

    public function show(Order $order)
    {
        $order->load(['mesa', 'items.menuItem', 'creator']);
        return view('content.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['mesa', 'items.menuItem']);

        // Mostrar mesas disponibles + la mesa actual de la orden (aunque esté ocupada)
        $mesas = Mesa::where('activa', true)
            ->where(function ($q) use ($order) {
                $q->where('estado', 'disponible')
                  ->orWhere('id', $order->mesa_id);
            })
            ->orderBy('numero')
            ->get();

        $menuItems = MenuItem::where('disponible', true)->orderBy('categoria')->orderBy('nombre')->get();

        return view('content.orders.edit', compact('order', 'mesas', 'menuItems'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $order) {
            // Solo se pueden modificar items si el status lo permite
            if ($order->canModifyItems()) {
                $order->items()->delete();

                foreach ($data['items'] as $item) {
                    $menuItem  = MenuItem::findOrFail($item['menu_item_id']);
                    $lineTotal = $menuItem->precio * $item['quantity'];
                    $order->items()->create([
                        'menu_item_id' => $menuItem->id,
                        'quantity'     => $item['quantity'],
                        'unit_price'   => $menuItem->precio,
                        'total'        => $lineTotal,
                    ]);
                }

                $order->recalculateTotal();
            }

            $order->update([
                'mesa_id'        => $data['type'] === 'mesa' ? ($data['mesa_id'] ?? null) : null,
                'type'           => $data['type'],
                'payment_method' => $data['payment_method'] ?? $order->payment_method,
                'split_count'    => $data['split_count'] ?? $order->split_count,
                'notes'          => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function destroy(Order $order)
    {
        if (!$order->canBeDeleted()) {
            return response()->json(['message' => 'Una orden pagada no puede eliminarse.'], 403);
        }

        $mesaId = $order->mesa_id;
        $order->delete();

        // Liberar la mesa si ya no tiene órdenes activas
        if ($mesaId) {
            $mesa = Mesa::find($mesaId);
            if ($mesa && !$mesa->orders()->whereNotIn('status', ['pagado'])->exists()) {
                $mesa->update(['estado' => 'disponible']);
            }
        }

        return response()->json(['message' => 'Orden eliminada correctamente.']);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $nextStatus = $order->getNextStatus();

        if (!$nextStatus) {
            return back()->with('error', 'La orden ya está en su estado final.');
        }

        if (!$order->items()->exists()) {
            return back()->with('error', 'Una orden sin platillos no puede avanzar de estado.');
        }

        // Validar inventario antes de pasar a en_preparacion
        if ($nextStatus === 'en_preparacion') {
            $order->load('items');
            $check = $this->inventoryService->canFulfillOrder($order);
            if (!$check['ok']) {
                $missing = collect($check['missing'])
                    ->map(fn($m) => "{$m['name']}: necesita {$m['needed']} {$m['unit']}, disponible {$m['available']}")
                    ->join('; ');
                return back()->with('error', "Inventario insuficiente — {$missing}.");
            }
        }

        $data = ['status' => $nextStatus];

        if ($nextStatus === 'pagado') {
            $request->validate([
                'payment_method' => ['required', 'in:efectivo,tarjeta,transferencia'],
            ], [
                'payment_method.required' => 'El método de pago es obligatorio para cerrar la orden.',
            ]);
            $data['payment_method'] = $request->payment_method;
        }

        DB::transaction(function () use ($order, $data, $nextStatus) {
            $order->update($data);

            // Descontar inventario al iniciar preparación
            if ($nextStatus === 'en_preparacion') {
                $this->inventoryService->deductForOrder($order, auth()->id());
            }
        });

        // Al pagar, liberar la mesa si ya no tiene órdenes activas
        if ($nextStatus === 'pagado' && $order->mesa_id) {
            $mesa = Mesa::find($order->mesa_id);
            if ($mesa && !$mesa->orders()->whereNotIn('status', ['pagado'])->exists()) {
                $mesa->update(['estado' => 'disponible']);
            }
        }

        return back()->with('success', 'Estado actualizado a: ' . Order::STATUS_LABELS[$nextStatus]['label']);
    }

    public function cocina()
    {
        $orders = Order::with(['mesa', 'items.menuItem'])
            ->whereNotIn('status', ['pagado'])
            ->orderByRaw("FIELD(status, 'en_preparacion', 'pendiente', 'listo', 'entregado')")
            ->get();

        return view('content.orders.cocina', compact('orders'));
    }
}
