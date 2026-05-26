@extends('layouts/layoutMaster')

@section('title', 'Pedido #' . str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT))

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-4">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
  $statusInfo = \App\Models\PurchaseOrder::STATUS_LABELS[$purchaseOrder->status];
  $transitions = \App\Models\PurchaseOrder::STATUS_TRANSITIONS[$purchaseOrder->status] ?? [];
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
  <div>
    <h4 class="mb-1">Pedido #{{ str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT) }}</h4>
    <p class="mb-0 text-muted">
      {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}
      &nbsp;·&nbsp;
      <span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
    </p>
  </div>
  <div class="d-flex gap-3 flex-wrap">
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-label-secondary">
      <i class="icon-base ti tabler-arrow-left me-1"></i>Volver
    </a>
    @if($purchaseOrder->canEdit())
      <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-label-warning">
        <i class="icon-base ti tabler-edit me-1"></i>Editar
      </a>
    @endif
    @foreach($transitions as $newStatus)
      @php $tInfo = \App\Models\PurchaseOrder::STATUS_LABELS[$newStatus]; @endphp
      <form action="{{ route('purchase-orders.status', [$purchaseOrder, $newStatus]) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-{{ $newStatus === 'cancelado' ? 'label-danger' : 'primary' }}"
          onclick="return confirm('¿Cambiar estado a {{ $tInfo['label'] }}?')">
          @if($newStatus === 'recibido')
            <i class="icon-base ti tabler-package-import me-1"></i>Marcar Recibido
          @elseif($newStatus === 'enviado')
            <i class="icon-base ti tabler-send me-1"></i>Marcar Enviado
          @else
            <i class="icon-base ti tabler-x me-1"></i>Cancelar Pedido
          @endif
        </button>
      </form>
    @endforeach
  </div>
</div>

<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Insumos del Pedido</h5></div>
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead class="table-light">
            <tr>
              <th>Insumo</th>
              <th class="text-center">Unidad</th>
              <th class="text-center">Cantidad</th>
              <th class="text-center">Costo Unit.</th>
              <th class="text-center">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($purchaseOrder->items as $item)
            <tr>
              <td class="fw-medium">{{ $item->inventoryItem?->name ?? '—' }}</td>
              <td class="text-center">{{ \App\Models\InventoryItem::UNITS[$item->inventoryItem?->unit] ?? '—' }}</td>
              <td class="text-center">{{ $item->quantity }}</td>
              <td class="text-center">${{ number_format($item->unit_cost, 2) }}</td>
              <td class="text-center fw-medium">${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="table-light">
            <tr>
              <td colspan="4" class="text-end fw-bold">Total</td>
              <td class="text-center fw-bold text-primary">${{ number_format($purchaseOrder->total, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    @if($purchaseOrder->notes)
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Notas</h5></div>
      <div class="card-body"><p class="mb-0">{{ $purchaseOrder->notes }}</p></div>
    </div>
    @endif
  </div>

  <div class="col-12 col-lg-4">
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Detalle</h5></div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-5 text-muted">Proveedor</dt>
          <dd class="col-7 fw-medium">{{ $purchaseOrder->supplier?->business_name ?? '—' }}</dd>

          <dt class="col-5 text-muted">Estado</dt>
          <dd class="col-7"><span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span></dd>

          <dt class="col-5 text-muted">Entrega</dt>
          <dd class="col-7">{{ $purchaseOrder->delivery_date?->format('d/m/Y') ?? '—' }}</dd>

          <dt class="col-5 text-muted">Total</dt>
          <dd class="col-7 fw-bold text-primary">${{ number_format($purchaseOrder->total, 2) }}</dd>

          <dt class="col-5 text-muted">Creado por</dt>
          <dd class="col-7">{{ $purchaseOrder->creator?->name ?? 'Sistema' }}</dd>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
