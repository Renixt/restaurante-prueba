@extends('layouts/layoutMaster')

@section('title', 'Mesas - SGR')

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
@vite('resources/assets/js/mesas/mesa-list.js')
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Mesas List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Filtros</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-6 mesas_estado"></div>
      <div class="col-md-6 mesas_activa"></div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-mesas table"
           data-csrf="{{ csrf_token() }}"
           data-mesas-base="{{ url('/mesas') }}">
      <thead class="border-top">
        <tr>
          <th></th>
          <th></th>
          <th>Número</th>
          <th>Capacidad</th>
          <th>Ubicación</th>
          <th>Estado</th>
          <th>Activa</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection
