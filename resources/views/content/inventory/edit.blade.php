@extends('layouts/layoutMaster')

@section('title', 'Editar Insumo - Inventario')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/inventory/inventory-form.js')
@endsection

@section('content')
<form action="{{ route('inventory.update', $inventory) }}" method="POST">
  @csrf
  @method('PUT')

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div>
      <h4 class="mb-1">Editar: {{ $inventory->name }}</h4>
      <p class="mb-0 text-muted">SKU: {{ $inventory->sku }}</p>
    </div>
    <div class="d-flex gap-4">
      <a href="{{ route('inventory.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <a href="{{ route('inventory.movements', $inventory) }}" class="btn btn-label-info">
        <i class="icon-base ti tabler-history me-1"></i>Movimientos
      </a>
      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible mb-6" role="alert">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if($inventory->isLowStock())
  <div class="alert alert-warning mb-6">
    <i class="icon-base ti tabler-alert-triangle me-1"></i>
    Stock bajo: <strong>{{ $inventory->current_stock }} {{ $inventory->unit }}</strong> (mínimo: {{ $inventory->minimum_stock }})
  </div>
  @endif

  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Datos del Insumo</h5></div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-8">
              <label class="form-label" for="inv-name">Nombre <span class="text-danger">*</span></label>
              <input type="text" id="inv-name" name="name"
                     class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $inventory->name) }}" required />
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="inv-sku">SKU <span class="text-danger">*</span></label>
              <input type="text" id="inv-sku" name="sku"
                     class="form-control @error('sku') is-invalid @enderror"
                     value="{{ old('sku', $inventory->sku) }}" required />
              @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="inv-stock">Stock actual <span class="text-danger">*</span></label>
              <input type="number" id="inv-stock" name="current_stock"
                     class="form-control @error('current_stock') is-invalid @enderror"
                     value="{{ old('current_stock', $inventory->current_stock) }}" min="0" step="0.001" required />
              @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="inv-min-stock">Stock mínimo <span class="text-danger">*</span></label>
              <input type="number" id="inv-min-stock" name="minimum_stock"
                     class="form-control @error('minimum_stock') is-invalid @enderror"
                     value="{{ old('minimum_stock', $inventory->minimum_stock) }}" min="0" step="0.001" required />
              @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="inv-cost">Costo unitario ($) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" id="inv-cost" name="cost"
                       class="form-control @error('cost') is-invalid @enderror"
                       value="{{ old('cost', $inventory->cost) }}" min="0" step="0.01" required />
              </div>
              @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card mb-6">
        <div class="card-header"><h5 class="card-title mb-0">Clasificación</h5></div>
        <div class="card-body">
          <div class="mb-4 ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="inv-unit">Unidad de medida <span class="text-danger">*</span></label>
            <select id="inv-unit" name="unit"
                    class="select2 form-select @error('unit') is-invalid @enderror"
                    data-placeholder="Selecciona unidad" required>
              @foreach($units as $key => $label)
              <option value="{{ $key }}" {{ old('unit', $inventory->unit) === $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4 ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="inv-supplier">Proveedor</label>
            <select id="inv-supplier" name="supplier_id"
                    class="select2 form-select"
                    data-placeholder="Sin proveedor">
              <option value="">Sin proveedor</option>
              @foreach($suppliers as $s)
              <option value="{{ $s->id }}" {{ old('supplier_id', $inventory->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->business_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="d-flex justify-content-between align-items-center border-top pt-4">
            <span class="fw-medium">Insumo activo</span>
            <div class="form-check form-switch me-n2">
              <input type="hidden" name="is_active" value="0" />
              <input type="checkbox" class="form-check-input" id="inv-active" name="is_active" value="1"
                     {{ old('is_active', $inventory->is_active) ? 'checked' : '' }} />
              <label class="form-check-label" for="inv-active"></label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
