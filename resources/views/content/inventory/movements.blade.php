@extends('layouts/layoutMaster')

@section('title', 'Movimientos — ' . $inventory->name)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Movimientos: {{ $inventory->name }}</h4>
    <p class="mb-0 text-muted">SKU: {{ $inventory->sku }} · Stock actual: <strong>{{ $inventory->current_stock }} {{ $inventory->unit }}</strong></p>
  </div>
  <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-label-secondary">
    <i class="icon-base ti tabler-arrow-left me-1"></i>Volver
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Fecha</th>
          <th>Tipo</th>
          <th class="text-end">Cantidad</th>
          <th class="text-end">Antes</th>
          <th class="text-end">Después</th>
          <th>Razón</th>
          <th>Usuario</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movements as $mov)
        <tr>
          <td class="text-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
          <td>
            @if($mov->type === 'entrada')
              <span class="badge bg-label-success">Entrada</span>
            @elseif($mov->type === 'salida')
              <span class="badge bg-label-danger">Salida</span>
            @else
              <span class="badge bg-label-info">Ajuste</span>
            @endif
          </td>
          <td class="text-end fw-medium">
            @if($mov->type === 'salida') <span class="text-danger">−</span> @else <span class="text-success">+</span> @endif
            {{ $mov->quantity }}
          </td>
          <td class="text-end text-muted">{{ $mov->before_stock }}</td>
          <td class="text-end fw-medium">{{ $mov->after_stock }}</td>
          <td>{{ $mov->reason ?? '—' }}</td>
          <td>{{ $mov->creator?->name ?? 'Sistema' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-5">Sin movimientos registrados.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($movements->hasPages())
  <div class="card-footer">{{ $movements->links() }}</div>
  @endif
</div>
@endsection
