@extends('layouts/layoutMaster')

@section('title', 'Editar Orden #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' - SGR')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

@section('page-script')
@vite('resources/assets/js/orders/order-form.js')
@endsection

@section('content')
<script>
  window.menuItemsData = @json($menuItems->keyBy('id')->map(fn($i) => [
    'id'       => $i->id,
    'nombre'   => $i->nombre,
    'categoria'=> $i->categoria,
    'precio'   => (float) $i->precio,
  ]));

  window.existingItems = @json($order->items->map(fn($oi) => [
    'menu_item_id' => $oi->menu_item_id,
    'nombre'       => $oi->menuItem->nombre ?? '—',
    'unit_price'   => (float) $oi->unit_price,
    'quantity'     => $oi->quantity,
    'total'        => (float) $oi->total,
  ]));
</script>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
  <div>
    <h4 class="mb-1">Orden #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h4>
    <p class="mb-0 text-muted">
      <span class="badge {{ $order->status_class }}">{{ $order->status_label }}</span>
      &mdash; {{ $order->created_at->format('d/m/Y H:i') }}
    </p>
  </div>
  <div class="d-flex gap-3">
    <a href="{{ route('orders.index') }}" class="btn btn-label-secondary">Volver</a>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-5" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-5" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  <!-- Columna principal -->
  <div class="col-12 col-lg-8">

    @if($order->canModifyItems())
    <!-- Formulario edición (solo en pendiente) -->
    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form">
      @csrf
      @method('PUT')

      @if($errors->any())
      <div class="alert alert-danger alert-dismissible mb-5">
        <ul class="mb-0">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <!-- Tipo y mesa -->
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Tipo de Orden</h5></div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-6">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="type-mesa"
                       value="mesa" {{ $order->type === 'mesa' ? 'checked' : '' }}>
                <label class="form-check-label" for="type-mesa">
                  <i class="icon-base ti tabler-armchair me-1 text-primary"></i>Mesa
                </label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="type-takeaway"
                       value="takeaway" {{ $order->type === 'takeaway' ? 'checked' : '' }}>
                <label class="form-check-label" for="type-takeaway">
                  <i class="icon-base ti tabler-shopping-bag me-1 text-warning"></i>Para llevar
                </label>
              </div>
            </div>
          </div>

          <div class="mt-4" id="mesa-section">
            <label class="form-label" for="mesa_id">Mesa</label>
            <select id="mesa_id" name="mesa_id" class="select2 form-select"
                    data-placeholder="Selecciona una mesa">
              <option value="">Selecciona una mesa</option>
              @foreach($mesas as $mesa)
              <option value="{{ $mesa->id }}" {{ $order->mesa_id == $mesa->id ? 'selected' : '' }}>
                Mesa {{ $mesa->numero }} (cap. {{ $mesa->capacidad }})
              </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Items -->
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Platillos</h5></div>
        <div class="card-body">
          <div class="row g-3 align-items-end mb-5">
            <div class="col-md-7">
              <label class="form-label" for="select-platillo">Platillo</label>
              <select id="select-platillo" class="select2 form-select"
                      data-placeholder="Busca y selecciona un platillo">
                <option value="">Busca un platillo...</option>
                @foreach($menuItems as $item)
                <option value="{{ $item->id }}" data-precio="{{ $item->precio }}"
                        data-nombre="{{ $item->nombre }}" data-categoria="{{ $item->categoria }}">
                  {{ $item->nombre }} — ${{ number_format($item->precio, 2) }} ({{ $item->categoria }})
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="input-cantidad">Cant.</label>
              <input type="number" id="input-cantidad" class="form-control" value="1" min="1" max="99">
            </div>
            <div class="col-md-3">
              <button type="button" id="btn-agregar-item" class="btn btn-label-primary w-100">
                <i class="icon-base ti tabler-plus me-1"></i>Agregar
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="items-table">
              <thead class="table-light">
                <tr>
                  <th>Platillo</th>
                  <th class="text-center" style="width:100px">Precio</th>
                  <th class="text-center" style="width:90px">Cant.</th>
                  <th class="text-center" style="width:110px">Subtotal</th>
                  <th class="text-center" style="width:60px"></th>
                </tr>
              </thead>
              <tbody id="items-body">
                <tr id="empty-row">
                  <td colspan="5" class="text-center text-muted py-4">Sin platillos</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div id="items-hidden"></div>
        </div>
      </div>

      <!-- Notas -->
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Notas</h5></div>
        <div class="card-body">
          <textarea name="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
        </div>
      </div>

      <!-- Guardar -->
      <div class="mb-6">
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-device-floppy me-1"></i>Guardar cambios
        </button>
      </div>
    </form>
    @else
    <!-- Vista de items (no editable) -->
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Platillos</h5></div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr>
              <th>Platillo</th>
              <th class="text-center">Precio</th>
              <th class="text-center">Cant.</th>
              <th class="text-center">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td>
                <span class="fw-medium">{{ $item->menuItem->nombre ?? '—' }}</span>
                <small class="d-block text-muted">{{ $item->menuItem->categoria ?? '' }}</small>
              </td>
              <td class="text-center">${{ number_format($item->unit_price, 2) }}</td>
              <td class="text-center">{{ $item->quantity }}</td>
              <td class="text-center fw-medium">${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td class="text-center fw-bold text-primary">${{ number_format($order->total, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      @if($order->notes)
      <div class="card-body border-top">
        <p class="text-muted mb-0"><strong>Notas:</strong> {{ $order->notes }}</p>
      </div>
      @endif
    </div>
    @endif

  </div>
  <!-- /Columna principal -->

  <!-- Columna lateral -->
  <div class="col-12 col-lg-4">

    <!-- Resumen totales -->
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Platillos</span>
          <span class="fw-medium">{{ $order->items->sum('quantity') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Subtotal</span>
          <span>${{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between border-top pt-3 mb-2">
          <span class="fw-bold">Total</span>
          <span class="fw-bold text-primary fs-5">${{ number_format($order->total, 2) }}</span>
        </div>
        @if($order->split_count > 1)
        <div class="text-muted small">
          Por persona ({{ $order->split_count }}): <strong>${{ $order->per_person }}</strong>
        </div>
        @endif
      </div>
    </div>

    <!-- Avanzar estado -->
    @php $nextStatus = $order->getNextStatus(); @endphp
    @if($nextStatus)
    <div class="card mb-6">
      <div class="card-header"><h5 class="card-title mb-0">Estado de la Orden</h5></div>
      <div class="card-body">
        <p class="mb-3">
          Estado actual:
          <span class="badge {{ $order->status_class }}">{{ $order->status_label }}</span>
        </p>

        <form action="{{ route('orders.status', $order) }}" method="POST">
          @csrf
          @method('PATCH')

          @if($nextStatus === 'pagado')
          <div class="mb-4">
            <label class="form-label" for="adv-payment-method">
              Método de pago <span class="text-danger">*</span>
            </label>
            <select id="adv-payment-method" name="payment_method" class="form-select" required>
              <option value="">Selecciona método</option>
              <option value="efectivo"     {{ $order->payment_method === 'efectivo'     ? 'selected' : '' }}>Efectivo</option>
              <option value="tarjeta"      {{ $order->payment_method === 'tarjeta'      ? 'selected' : '' }}>Tarjeta</option>
              <option value="transferencia"{{ $order->payment_method === 'transferencia'? 'selected' : '' }}>Transferencia</option>
            </select>
          </div>
          @endif

          @php
            $nextLabel = \App\Models\Order::STATUS_LABELS[$nextStatus]['label'] ?? $nextStatus;
            $btnClass  = $nextStatus === 'pagado' ? 'btn-success' : 'btn-info';
          @endphp
          <button type="submit" class="btn {{ $btnClass }} w-100">
            <i class="icon-base ti tabler-arrow-right me-1"></i>
            Avanzar a: {{ $nextLabel }}
          </button>
        </form>
      </div>
    </div>
    @else
    <div class="card mb-6">
      <div class="card-body text-center">
        <span class="badge bg-label-secondary fs-6 py-2 px-4">Orden Completada</span>
        <p class="text-muted mt-2 mb-0">Esta orden ya fue pagada y cerrada.</p>
      </div>
    </div>
    @endif

    <!-- Pago registrado -->
    @if($order->payment_method)
    <div class="card mb-6">
      <div class="card-body">
        <p class="mb-0">
          <i class="icon-base ti tabler-credit-card me-1 text-success"></i>
          Pago: <strong>{{ ucfirst($order->payment_method) }}</strong>
        </p>
      </div>
    </div>
    @endif

    <!-- Mesa / Tipo -->
    <div class="card mb-6">
      <div class="card-body">
        @if($order->type === 'mesa' && $order->mesa)
        <p class="mb-0">
          <i class="icon-base ti tabler-armchair me-1 text-primary"></i>
          Mesa: <strong>{{ $order->mesa->numero }}</strong>
          (cap. {{ $order->mesa->capacidad }})
        </p>
        @else
        <p class="mb-0">
          <i class="icon-base ti tabler-shopping-bag me-1 text-warning"></i>
          <strong>Para llevar</strong>
        </p>
        @endif
      </div>
    </div>

  </div>
  <!-- /Columna lateral -->
</div>

@endsection
