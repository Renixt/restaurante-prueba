@extends('layouts/layoutMaster')

@section('title', 'Órdenes - SGR')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

@section('page-script')
@vite('resources/assets/js/orders/order-list.js')
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-4" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-1">Gestión de Órdenes</h5>
      <p class="text-muted mb-0 small">Listado de todas las órdenes del restaurante</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('orders.cocina') }}" class="btn btn-label-info">
        <i class="icon-base ti tabler-chef-hat me-1"></i>Vista Cocina
      </a>
      <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-1"></i>Nueva Orden
      </a>
    </div>
  </div>

  <!-- Filtros -->
  <div class="card-header border-bottom py-3">
    <div class="row g-3">
      <div class="col-md-4 orders_status"></div>
      <div class="col-md-4 orders_type"></div>
    </div>
  </div>

  <div class="card-datatable table-responsive">
    <table class="datatables-orders table"
           data-csrf="{{ csrf_token() }}"
           data-orders-base="{{ url('/orders') }}">
      <thead class="border-top">
        <tr>
          <th></th>
          <th></th>
          <th>Folio</th>
          <th>Mesa / Tipo</th>
          <th>Estatus</th>
          <th>Total</th>
          <th>Pago</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection
