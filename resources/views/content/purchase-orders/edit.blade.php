@extends('layouts/layoutMaster')

@section('title', 'Editar Pedido - SGR')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/purchase-orders/purchase-order-form.js')
@endsection

@section('content')

@php
  $inventoryData   = $inventoryItems->keyBy('id')->map(fn($i) => [
    'id' => $i->id, 'name' => $i->name, 'unit' => $i->unit, 'cost' => (float) $i->cost,
  ]);
  $existingItems = $purchaseOrder->items->map(fn($item) => [
    'inventory_item_id' => $item->inventory_item_id,
    'name'     => $item->inventoryItem?->name ?? '',
    'unit'     => $item->inventoryItem?->unit ?? '',
    'quantity' => (float) $item->quantity,
    'unit_cost'=> (float) $item->unit_cost,
    'total'    => (float) $item->total,
  ])->values()->toArray();
@endphp

<script>
  window.inventoryItemsData = @json($inventoryData);
  window.existingPoItems    = @json($existingItems);
</script>

<form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" id="po-form">
  @csrf
  @method('PUT')

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div>
      <h4 class="mb-1">Editar Pedido #{{ str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT) }}</h4>
      <p class="mb-0 text-muted">{{ $purchaseOrder->supplier?->business_name }}</p>
    </div>
    <div class="d-flex gap-3">
      <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="icon-base ti tabler-check me-1"></i>Guardar Cambios
      </button>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible mb-6">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Insumos del Pedido</h5></div>
        <div class="card-body">
          <div class="row g-3 align-items-end mb-5">
            <div class="col-md-5">
              <label class="form-label" for="select-insumo-po">Insumo</label>
              <select id="select-insumo-po" class="select2 form-select" data-placeholder="Selecciona un insumo">
                <option value="">Selecciona...</option>
                @foreach($inventoryItems as $inv)
                <option value="{{ $inv->id }}"
                        data-nombre="{{ $inv->name }}"
                        data-unit="{{ $inv->unit }}"
                        data-cost="{{ $inv->cost }}">
                  {{ $inv->name }} ({{ \App\Models\InventoryItem::UNITS[$inv->unit] ?? $inv->unit }})
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="input-qty-po">Cantidad</label>
              <input type="number" id="input-qty-po" class="form-control" value="1" min="0.001" step="0.001">
            </div>
            <div class="col-md-3">
              <label class="form-label" for="input-cost-po">Costo unitario ($)</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" id="input-cost-po" class="form-control" value="0" min="0" step="0.01">
              </div>
            </div>
            <div class="col-md-2">
              <button type="button" id="btn-agregar-insumo-po" class="btn btn-label-primary w-100">
                <i class="icon-base ti tabler-plus me-1"></i>Agregar
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="po-items-table">
              <thead class="table-light">
                <tr>
                  <th>Insumo</th>
                  <th class="text-center" style="width:100px">Unidad</th>
                  <th class="text-center" style="width:100px">Cantidad</th>
                  <th class="text-center" style="width:120px">Costo Unit.</th>
                  <th class="text-center" style="width:120px">Subtotal</th>
                  <th class="text-center" style="width:60px"></th>
                </tr>
              </thead>
              <tbody id="po-items-body">
                <tr id="po-empty-row">
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="icon-base ti tabler-package icon-24px d-block mb-1"></i>
                    Sin insumos en el pedido
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div id="po-items-hidden"></div>
        </div>
      </div>

      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Notas</h5></div>
        <div class="card-body">
          <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Detalles del Pedido</h5></div>
        <div class="card-body">
          <div class="mb-4 ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="po-supplier">Proveedor <span class="text-danger">*</span></label>
            <select id="po-supplier" name="supplier_id"
                    class="select2 form-select @error('supplier_id') is-invalid @enderror"
                    data-placeholder="Selecciona proveedor" required>
              @foreach($suppliers as $s)
              <option value="{{ $s->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->business_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-4">
            <label class="form-label" for="po-delivery">Fecha estimada de entrega</label>
            <input type="date" id="po-delivery" name="delivery_date" class="form-control"
                   value="{{ old('delivery_date', $purchaseOrder->delivery_date?->format('Y-m-d')) }}" />
          </div>

          <div class="d-flex justify-content-between mb-3 border-top pt-3">
            <span class="fw-bold">Total estimado</span>
            <span id="po-summary-total" class="fw-bold text-primary fs-5">$0.00</span>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="icon-base ti tabler-check me-1"></i>Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
