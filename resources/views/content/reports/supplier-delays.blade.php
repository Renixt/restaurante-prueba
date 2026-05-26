@extends('layouts/layoutMaster')

@section('title', 'Pedidos Vencidos - SGR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Pedidos a Proveedores Vencidos</h4>
    <p class="mb-0"><a href="{{ route('reports.index') }}">Reportes</a> / Pedidos vencidos</p>
  </div>
</div>

<div class="card">
  <div class="card-datatable table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Proveedor</th>
          <th>Estado</th>
          <th>Fecha entrega</th>
          <th>Días de retraso</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        <tr>
          <td>{{ $order->id }}</td>
          <td>{{ $order->supplier->business_name }}</td>
          <td>
            <span class="badge bg-label-warning text-capitalize">{{ $order->status }}</span>
          </td>
          <td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</td>
          <td class="text-danger fw-bold">
            {{ \Carbon\Carbon::parse($order->delivery_date)->diffInDays(now()) }} días
          </td>
          <td>
            <a href="{{ route('purchase-orders.show', $order) }}" class="btn btn-sm btn-label-primary">Ver</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">No hay pedidos vencidos.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
