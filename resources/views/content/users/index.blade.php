@extends('layouts/layoutMaster')

@section('title', 'Usuarios - SGR')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/admin/user-list.js')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Usuarios</h4>
    <p class="mb-0">Gestión de cuentas del sistema</p>
  </div>
  <a href="{{ route('users.create') }}" class="btn btn-primary">
    <i class="icon-base ti tabler-plus me-1"></i> Nuevo Usuario
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card">
  <div class="card-datatable">
    <table class="datatables-sgr-users table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection
