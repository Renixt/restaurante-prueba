@extends('layouts/layoutMaster')

@section('title', 'Vista Cocina - SGR')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">
      <i class="icon-base ti tabler-chef-hat me-2 text-primary"></i>Vista Cocina
    </h4>
    <p class="text-muted mb-0 small">
      Actualización automática cada 30 segundos &mdash;
      <span id="last-update" class="fw-medium">ahora</span>
    </p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <span class="badge bg-label-secondary" id="order-count">{{ $orders->count() }} órdenes</span>
    <a href="{{ route('orders.index') }}" class="btn btn-label-secondary btn-sm">
      <i class="icon-base ti tabler-list me-1"></i>Lista completa
    </a>
    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
      <i class="icon-base ti tabler-plus me-1"></i>Nueva
    </a>
  </div>
</div>

<!-- Leyenda de estados -->
<div class="d-flex flex-wrap gap-3 mb-6">
  <span class="badge bg-label-warning px-3 py-2">Pendiente</span>
  <span class="badge bg-label-info px-3 py-2">En preparación</span>
  <span class="badge bg-label-success px-3 py-2">Listo</span>
  <span class="badge bg-label-primary px-3 py-2">Entregado</span>
</div>

<!-- Grid de órdenes -->
<div class="row g-4" id="cocina-grid">
  @forelse($orders as $order)
  @php
    $borderColors = [
      'pendiente'      => 'border-warning',
      'en_preparacion' => 'border-info',
      'listo'          => 'border-success',
      'entregado'      => 'border-primary',
    ];
    $border = $borderColors[$order->status] ?? 'border-secondary';
  @endphp
  <div class="col-12 col-sm-6 col-xl-4" data-order-id="{{ $order->id }}">
    <div class="card border border-2 {{ $border }} h-100">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
          <h6 class="mb-0 fw-bold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h6>
          <small class="text-muted">
            @if($order->type === 'mesa' && $order->mesa)
              <i class="ti tabler-armchair me-1"></i>Mesa {{ $order->mesa->numero }}
            @else
              <i class="ti tabler-shopping-bag me-1"></i>Para llevar
            @endif
          </small>
        </div>
        <div class="text-end">
          <span class="badge {{ $order->status_class }}">{{ $order->status_label }}</span>
          <small class="d-block text-muted mt-1">{{ $order->created_at->diffForHumans() }}</small>
        </div>
      </div>

      <div class="card-body py-3">
        <ul class="list-unstyled mb-3">
          @foreach($order->items as $item)
          <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
            <span class="fw-medium">
              <span class="badge bg-label-secondary me-2 py-1">×{{ $item->quantity }}</span>
              {{ $item->menuItem->nombre ?? '—' }}
            </span>
          </li>
          @endforeach
        </ul>

        @if($order->notes)
        <div class="alert alert-warning py-2 mb-0 small">
          <i class="ti tabler-info-circle me-1"></i>{{ $order->notes }}
        </div>
        @endif
      </div>

      @if($order->getNextStatus())
      <div class="card-footer py-2">
        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-flex gap-2 align-items-center">
          @csrf
          @method('PATCH')

          @if($order->getNextStatus() === 'pagado')
          <select name="payment_method" class="form-select form-select-sm" required>
            <option value="">Pago...</option>
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia</option>
          </select>
          @endif

          @php $nextLabel = \App\Models\Order::STATUS_LABELS[$order->getNextStatus()]['label'] ?? ''; @endphp
          <button type="submit" class="btn btn-sm btn-primary ms-auto text-nowrap">
            {{ $nextLabel }} →
          </button>
        </form>
      </div>
      @endif
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-10">
        <i class="icon-base ti tabler-coffee icon-48px text-muted d-block mb-3"></i>
        <h5 class="text-muted">No hay órdenes activas</h5>
        <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">Nueva Orden</a>
      </div>
    </div>
  </div>
  @endforelse
</div>

<script>
  // Auto-refresh cocina cada 30 segundos
  (function () {
    const updateLabel = document.getElementById('last-update');
    const countLabel  = document.getElementById('order-count');

    function formatTime(d) {
      return d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    }

    setInterval(function () {
      fetch('{{ route('orders.cocina') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (res) { return res.text(); })
        .then(function (html) {
          const parser = new DOMParser();
          const doc    = parser.parseFromString(html, 'text/html');
          const newGrid = doc.getElementById('cocina-grid');
          if (newGrid) {
            document.getElementById('cocina-grid').innerHTML = newGrid.innerHTML;
          }
          const newCount = doc.getElementById('order-count');
          if (newCount && countLabel) countLabel.textContent = newCount.textContent;
          if (updateLabel) updateLabel.textContent = formatTime(new Date());
        })
        .catch(function () { /* ignorar errores de red silenciosamente */ });
    }, 30000);
  })();
</script>

@endsection
