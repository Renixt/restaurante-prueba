@extends('layouts/layoutMaster')

@section('title', 'Nuevo Proveedor - SGR')

@section('page-script')
@vite('resources/assets/js/suppliers/supplier-form.js')
@endsection

@section('content')
<form action="{{ route('suppliers.store') }}" method="POST">
  @csrf

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div>
      <h4 class="mb-1">Nuevo Proveedor</h4>
      <p class="mb-0">Registra los datos del proveedor</p>
    </div>
    <div class="d-flex gap-4">
      <a href="{{ route('suppliers.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible mb-6" role="alert">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Datos del Proveedor</h5></div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label" for="sup-name">Razón Social <span class="text-danger">*</span></label>
              <input type="text" id="sup-name" name="business_name"
                     class="form-control @error('business_name') is-invalid @enderror"
                     value="{{ old('business_name') }}" placeholder="Ej. Distribuidora Carnes S.A. de C.V." required />
              @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="sup-rfc">RFC <span class="text-danger">*</span></label>
              <input type="text" id="sup-rfc" name="rfc"
                     class="form-control @error('rfc') is-invalid @enderror"
                     value="{{ old('rfc') }}" placeholder="Ej. XAXX010101000" maxlength="13" required />
              @error('rfc')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="sup-phone">Teléfono</label>
              <input type="text" id="sup-phone" name="phone"
                     class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone') }}" placeholder="Ej. 5512345678" />
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="sup-email">Correo electrónico</label>
              <input type="email" id="sup-email" name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" placeholder="proveedor@empresa.com" />
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="sup-status">Estado <span class="text-danger">*</span></label>
              <select id="sup-status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="activo" {{ old('status', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('status') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
              </select>
              @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label" for="sup-address">Dirección</label>
              <textarea id="sup-address" name="address" class="form-control" rows="3"
                        placeholder="Calle, número, colonia, ciudad...">{{ old('address') }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
