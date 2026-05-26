@extends('layouts/layoutMaster')

@section('title', 'Editar Rol - SGR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1 text-capitalize">Rol: {{ $role->name }}</h4>
    <p class="mb-0"><a href="{{ route('roles.index') }}">Roles</a> / Editar permisos</p>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('roles.update', $role) }}">
      @csrf
      @method('PUT')

      @php
        $groups = $permissions->groupBy(fn($p) => explode('.', $p)[0]);
      @endphp

      <div class="row g-4">
        @foreach($groups as $group => $groupPerms)
        <div class="col-md-6 col-lg-4">
          <div class="border rounded p-4">
            <h6 class="mb-3 text-uppercase text-muted" style="font-size:0.75rem; letter-spacing:0.05em;">{{ $group }}</h6>
            @foreach($groupPerms as $perm)
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="permissions[]"
                id="perm_{{ str_replace(['.', '-'], '_', $perm) }}"
                value="{{ $perm }}"
                {{ in_array($perm, $rolePermissions) ? 'checked' : '' }}>
              <label class="form-check-label" for="perm_{{ str_replace(['.', '-'], '_', $perm) }}">
                {{ Str::after($perm, '.') }}
              </label>
            </div>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      <div class="mt-6">
        <button type="submit" class="btn btn-primary me-2">Guardar Permisos</button>
        <a href="{{ route('roles.index') }}" class="btn btn-label-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
