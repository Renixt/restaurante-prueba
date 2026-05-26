@extends('layouts/layoutMaster')

@section('title', 'Nueva Mesa - SGR')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

@section('page-script')
@vite('resources/assets/js/mesas/mesa-form.js')
@endsection

@section('content')
<form action="{{ route('mesas.store') }}" method="POST">
  @csrf

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Nueva Mesa</h4>
      <p class="mb-0">Completa los datos para registrar la mesa</p>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <a href="{{ route('mesas.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Guardar Mesa</button>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible mb-6" role="alert">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <div class="row">
    <!-- Columna principal -->
    <div class="col-12 col-lg-8">

      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Información de la Mesa</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="mesa-numero">Número / Nombre <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('numero') is-invalid @enderror"
                   id="mesa-numero"
                   name="numero"
                   value="{{ old('numero') }}"
                   placeholder="Ej. 1, Terraza 2, VIP..."
                   required />
            @error('numero')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-6">
            <label class="form-label" for="mesa-capacidad">Capacidad (personas) <span class="text-danger">*</span></label>
            <input type="number"
                   class="form-control @error('capacidad') is-invalid @enderror"
                   id="mesa-capacidad"
                   name="capacidad"
                   value="{{ old('capacidad', 4) }}"
                   min="1"
                   max="50"
                   required />
            @error('capacidad')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-0">
            <label class="form-label" for="mesa-ubicacion">Ubicación</label>
            <input type="text"
                   class="form-control @error('ubicacion') is-invalid @enderror"
                   id="mesa-ubicacion"
                   name="ubicacion"
                   value="{{ old('ubicacion') }}"
                   placeholder="Ej. Terraza, Salón principal, VIP..." />
            @error('ubicacion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

    </div>
    <!-- /Columna principal -->

    <!-- Columna lateral -->
    <div class="col-12 col-lg-4">

      <!-- Estado -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Estado</h5>
        </div>
        <div class="card-body">
          <div class="mb-4 ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="mesa-estado">Estado <span class="text-danger">*</span></label>
            <select id="mesa-estado"
                    class="select2 form-select @error('estado') is-invalid @enderror"
                    name="estado"
                    data-placeholder="Selecciona un estado"
                    required>
              @foreach($estados as $key => $info)
              <option value="{{ $key }}" {{ old('estado', 'disponible') === $key ? 'selected' : '' }}>
                {{ $info['label'] }}
              </option>
              @endforeach
            </select>
            @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-between align-items-center border-top pt-4">
            <span class="fw-medium">Mesa activa</span>
            <div class="form-check form-switch me-n2">
              <input type="hidden" name="activa" value="0" />
              <input type="checkbox"
                     class="form-check-input"
                     id="mesa-activa"
                     name="activa"
                     value="1"
                     {{ old('activa', true) ? 'checked' : '' }} />
              <label class="form-check-label" for="mesa-activa"></label>
            </div>
          </div>
        </div>
      </div>
      <!-- /Estado -->

    </div>
    <!-- /Columna lateral -->
  </div>
</form>
@endsection
