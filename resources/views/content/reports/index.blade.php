@extends('layouts/layoutMaster')

@section('title', 'Reportes - SGR')

@section('content')
<div class="mb-6">
  <h4 class="mb-1">Reportes</h4>
  <p class="mb-0">Análisis y exportación de datos del restaurante</p>
</div>

<div class="row g-6">
  <div class="col-md-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex flex-column">
        <div class="avatar mb-4">
          <span class="avatar-initial rounded bg-label-success">
            <i class="icon-base ti tabler-currency-peso icon-26px"></i>
          </span>
        </div>
        <h5 class="mb-2">Ventas</h5>
        <p class="text-muted mb-4 flex-grow-1">Reporte de órdenes pagadas por rango de fechas.</p>
        <a href="{{ route('reports.sales') }}" class="btn btn-label-success">Ver Reporte</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex flex-column">
        <div class="avatar mb-4">
          <span class="avatar-initial rounded bg-label-primary">
            <i class="icon-base ti tabler-tools-kitchen-2 icon-26px"></i>
          </span>
        </div>
        <h5 class="mb-2">Platillos más vendidos</h5>
        <p class="text-muted mb-4 flex-grow-1">Ranking de platillos por cantidad e ingresos.</p>
        <a href="{{ route('reports.top-dishes') }}" class="btn btn-label-primary">Ver Reporte</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex flex-column">
        <div class="avatar mb-4">
          <span class="avatar-initial rounded bg-label-warning">
            <i class="icon-base ti tabler-package icon-26px"></i>
          </span>
        </div>
        <h5 class="mb-2">Stock bajo</h5>
        <p class="text-muted mb-4 flex-grow-1">Insumos por debajo del stock mínimo.</p>
        <a href="{{ route('reports.low-stock') }}" class="btn btn-label-warning">Ver Reporte</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex flex-column">
        <div class="avatar mb-4">
          <span class="avatar-initial rounded bg-label-danger">
            <i class="icon-base ti tabler-truck icon-26px"></i>
          </span>
        </div>
        <h5 class="mb-2">Pedidos vencidos</h5>
        <p class="text-muted mb-4 flex-grow-1">Pedidos a proveedores con fecha de entrega superada.</p>
        <a href="{{ route('reports.supplier-delays') }}" class="btn btn-label-danger">Ver Reporte</a>
      </div>
    </div>
  </div>
</div>
@endsection
