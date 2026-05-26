@extends('layouts/layoutMaster')

@section('title', 'Stock Bajo - SGR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Insumos con Stock Bajo</h4>
    <p class="mb-0"><a href="{{ route('reports.index') }}">Reportes</a> / Stock Bajo</p>
  </div>
</div>

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Insumo</th>
          <th>SKU</th>
          <th>Stock actual</th>
          <th>Stock mínimo</th>
          <th>Unidad</th>
          <th>Proveedor</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr>
          <td>
            <a href="{{ route('inventory.edit', $item) }}">{{ $item->name }}</a>
          </td>
          <td>{{ $item->sku }}</td>
          <td class="text-danger fw-bold">{{ number_format($item->current_stock, 3) }}</td>
          <td>{{ number_format($item->minimum_stock, 3) }}</td>
          <td>{{ $item->unit }}</td>
          <td>{{ $item->supplier?->business_name ?? '—' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">No hay insumos con stock bajo.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
