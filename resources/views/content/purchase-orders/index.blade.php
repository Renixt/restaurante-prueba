@extends('layouts/layoutMaster')

@section('title', 'Pedidos a Proveedores - SGR')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
])
@endsection

@section('page-script')
@vite('resources/assets/js/purchase-orders/purchase-order-list.js')
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Filtros</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-6 po_status"></div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-purchase-orders table"
           data-csrf="{{ csrf_token() }}"
           data-po-base="{{ url('/purchase-orders') }}">
      <thead class="border-top">
        <tr>
          <th></th>
          <th></th>
          <th>Folio</th>
          <th>Proveedor</th>
          <th>Estado</th>
          <th>Total</th>
          <th>Entrega</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection
