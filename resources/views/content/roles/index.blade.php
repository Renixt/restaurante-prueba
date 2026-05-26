@extends('layouts/layoutMaster')

@section('title', 'Roles - SGR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Roles y Permisos</h4>
    <p class="mb-0">Gestión de roles del sistema</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-6">
  @foreach($roles as $role)
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">{{ $role->users_count }} {{ Str::plural('usuario', $role->users_count) }}</h6>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div>
            <h5 class="mb-1 text-capitalize">{{ $role->name }}</h5>
            <small class="text-muted">{{ $role->permissions->count() }} permisos asignados</small>
          </div>
          <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-label-primary">
            <i class="icon-base ti tabler-edit icon-sm me-1"></i> Editar
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
