@extends('layouts/layoutMaster')

@section('title', 'Editar Platillo - Menú')

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
@vite('resources/assets/js/menu/menu-item-form.js')
@endsection

@section('content')
<form action="{{ route('menu.update', $menuItem) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Editar Platillo</h4>
      <p class="mb-0">{{ $menuItem->nombre }}</p>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <a href="{{ route('menu.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Actualizar Platillo</button>
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

      <!-- Información del platillo -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Información del Platillo</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="menu-nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="menu-nombre"
                   name="nombre"
                   value="{{ old('nombre', $menuItem->nombre) }}"
                   placeholder="Ej. Tacos al Pastor"
                   required />
            @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-6">
            <label class="form-label" for="menu-descripcion">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                      id="menu-descripcion"
                      name="descripcion"
                      rows="4"
                      placeholder="Descripción del platillo (mínimo 10 caracteres)">{{ old('descripcion', $menuItem->descripcion) }}</textarea>
            @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      <!-- /Información del platillo -->

      <!-- Imagen -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="mb-0 card-title">Imagen del Platillo</h5>
        </div>
        <div class="card-body">
          @if($menuItem->imagen)
          <div class="mb-3">
            <p class="text-muted small mb-2">Imagen actual:</p>
            <img src="{{ asset('storage/' . $menuItem->imagen) }}"
                 alt="{{ $menuItem->nombre }}"
                 class="rounded img-fluid mb-2"
                 style="max-height: 160px;" />
          </div>
          @endif

          <div class="mb-3">
            <label class="form-label" for="menu-imagen">Nueva imagen (JPG / PNG, máx. 2 MB) — dejar vacío para mantener la actual</label>
            <input type="file"
                   class="form-control @error('imagen') is-invalid @enderror"
                   id="menu-imagen"
                   name="imagen"
                   accept="image/jpeg,image/png" />
            @error('imagen')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div id="menu-imagen-preview" class="d-none mt-3">
            <img src="" alt="Vista previa" class="rounded img-fluid" style="max-height: 200px;" />
          </div>
        </div>
      </div>
      <!-- /Imagen -->

    </div>
    <!-- /Columna principal -->

    <!-- Columna lateral -->
    <div class="col-12 col-lg-4">

      <!-- Precio y Disponibilidad -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Precio</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="menu-precio">Precio (MXN) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number"
                     class="form-control @error('precio') is-invalid @enderror"
                     id="menu-precio"
                     name="precio"
                     value="{{ old('precio', $menuItem->precio) }}"
                     placeholder="0.00"
                     step="0.01"
                     min="0.01"
                     required />
              @error('precio')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center border-top pt-4">
            <span class="fw-medium">Disponible</span>
            <div class="form-check form-switch me-n2">
              <input type="hidden" name="disponible" value="0" />
              <input type="checkbox"
                     class="form-check-input"
                     id="menu-disponible"
                     name="disponible"
                     value="1"
                     {{ old('disponible', $menuItem->disponible) ? 'checked' : '' }} />
              <label class="form-check-label" for="menu-disponible"></label>
            </div>
          </div>
        </div>
      </div>
      <!-- /Precio y Disponibilidad -->

      <!-- Categoría -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Organización</h5>
        </div>
        <div class="card-body">
          <div class="mb-4 ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="menu-categoria">
              Categoría <span class="text-danger">*</span>
            </label>
            <select id="menu-categoria"
                    class="select2 form-select @error('categoria') is-invalid @enderror"
                    name="categoria"
                    data-placeholder="Selecciona una categoría"
                    required>
              <option value="">Selecciona una categoría</option>
              @foreach($categorias as $cat)
              <option value="{{ $cat }}" {{ old('categoria', $menuItem->categoria) === $cat ? 'selected' : '' }}>
                {{ $cat }}
              </option>
              @endforeach
            </select>
            @error('categoria')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      <!-- /Categoría -->

    </div>
    <!-- /Columna lateral -->
  </div>
</form>
@endsection
