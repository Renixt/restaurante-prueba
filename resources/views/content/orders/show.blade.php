@extends('layouts/layoutMaster')

@section('title', 'Orden #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' - SGR')

@section('content')

  <div
    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div>
      <h4 class="mb-1">Orden {{ '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h4>
      <p class="mb-0 text-muted">
        {{ $order->created_at->format('d/m/Y H:i') }}
        &nbsp;·&nbsp;
        <span class="badge {{ $order->status_class }}">{{ $order->status_label }}</span>
      </p>
    </div>
    <div class="d-flex gap-3">
      <a href="{{ route('orders.index') }}" class="btn btn-label-secondary">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Volver
      </a>
      @if($order->canBeDeleted())
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
          <i class="icon-base ti tabler-edit me-1"></i>Editar
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-6" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row">
    <!-- Columna principal -->
    <div class="col-12 col-lg-8">

      <!-- Platillos -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Platillos</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead class="table-light">
              <tr>
                <th>Platillo</th>
                <th class="text-center" style="width:110px">Precio</th>
                <th class="text-center" style="width:80px">Cant.</th>
                <th class="text-center" style="width:110px">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $item)
                <tr>
                  <td class="fw-medium">{{ $item->menuItem->nombre ?? '—' }}</td>
                  <td class="text-center">${{ number_format($item->unit_price, 2) }}</td>
                  <td class="text-center">{{ $item->quantity }}</td>
                  <td class="text-center fw-medium">${{ number_format($item->total, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot class="table-light">
              <tr>
                <td colspan="3" class="text-end fw-bold">Total</td>
                <td class="text-center fw-bold text-primary">${{ number_format($order->total, 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      @if($order->notes)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Notas</h5>
          </div>
          <div class="card-body">
            <p class="mb-0">{{ $order->notes }}</p>
          </div>
        </div>
      @endif

    </div>
    <!-- /Columna principal -->

    <!-- Columna lateral -->
    <div class="col-12 col-lg-4">

      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Detalle</h5>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-5 text-muted">Tipo</dt>
            <dd class="col-7">
              @if($order->type === 'mesa')
                <i class="icon-base ti tabler-armchair me-1 text-primary"></i>Mesa
              @else
                <i class="icon-base ti tabler-shopping-bag me-1 text-warning"></i>Para llevar
              @endif
            </dd>

            @if($order->mesa)
              <dt class="col-5 text-muted">Mesa</dt>
              <dd class="col-7 fw-medium">Mesa {{ $order->mesa->numero }}</dd>
            @endif

            <dt class="col-5 text-muted">Estado</dt>
            <dd class="col-7">
              <span class="badge {{ $order->status_class }}">{{ $order->status_label }}</span>
            </dd>

            @if($order->payment_method)
              <dt class="col-5 text-muted">Pago</dt>
              <dd class="col-7 text-capitalize">{{ $order->payment_method }}</dd>
            @endif

            @if($order->split_count > 1)
              <dt class="col-5 text-muted">Dividir</dt>
              <dd class="col-7">{{ $order->split_count }} personas · <strong>${{ number_format($order->per_person, 2) }}</strong> c/u</dd>
            @endif
          </dl>
        </div>
      </div>

      <!-- Avanzar estado -->
      @php $nextStatus = $order->getNextStatus(); @endphp
      @if($nextStatus)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Avanzar Estado</h5>
          </div>
          <div class="card-body">
            <form action="{{ route('orders.status', $order) }}" method="POST">
              @csrf
              @method('PATCH')

              @if($nextStatus === 'pagado')
                <div class="mb-4">
                  <label class="form-label" for="payment_method_show">Método de pago <span class="text-danger">*</span></label>
                  <select name="payment_method" id="payment_method_show" class="form-select" required>
                    <option value="">Selecciona...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                  </select>
                </div>
              @endif

              <button type="submit" class="btn btn-primary w-100">
                <i class="icon-base ti tabler-arrow-right me-1"></i>
                Pasar a: {{ \App\Models\Order::STATUS_LABELS[$nextStatus]['label'] }}
              </button>
            </form>
          </div>
        </div>
      @endif

    </div>
    <!-- /Columna lateral -->
  </div>

@endsection
