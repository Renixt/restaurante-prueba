@extends('layouts/layoutMaster')

@section('title', 'Editar Usuario - SGR')

@section('page-script')
@vite('resources/assets/js/admin/user-form.js')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Editar Usuario</h4>
    <p class="mb-0"><a href="{{ route('users.index') }}">Usuarios</a> / Editar</p>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('users.update', $user) }}">
      @csrf
      @method('PUT')

      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label" for="name">Nombre</label>
          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password">Nueva Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
        </div>

        <div class="col-md-6">
          <label class="form-label" for="role">Rol</label>
          <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="">Seleccionar rol</option>
            @foreach($roles as $role)
              <option value="{{ $role }}" {{ (old('role', $currentRole) === $role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
            @endforeach
          </select>
          @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" class="btn btn-primary me-2">Actualizar</button>
        <a href="{{ route('users.index') }}" class="btn btn-label-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
