<?php

namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return view('content.suppliers.index');
    }

    public function data()
    {
        $suppliers = Supplier::withCount(['inventoryItems', 'purchaseOrders'])->get()->map(function ($s) {
            return [
                'id'             => $s->id,
                'business_name'  => $s->business_name,
                'rfc'            => $s->rfc,
                'phone'          => $s->phone ?? '—',
                'email'          => $s->email ?? '—',
                'status'         => $s->status,
                'status_label'   => $s->status === 'activo' ? 'Activo' : 'Inactivo',
                'items_count'    => $s->inventory_items_count,
                'orders_count'   => $s->purchase_orders_count,
            ];
        });

        return response()->json(['data' => $suppliers]);
    }

    public function create()
    {
        return view('content.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Supplier $supplier)
    {
        return view('content.suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists() || $supplier->inventoryItems()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un proveedor con pedidos o insumos asociados.'
            ], 403);
        }

        $supplier->delete();
        return response()->json(['message' => 'Proveedor eliminado.']);
    }
}
