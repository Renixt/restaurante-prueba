@extends('layouts/layoutMaster')

@section('title', 'Receta: ' . $menuItem->nombre)

@section('page-script')
@vite('resources/assets/js/inventory/recipe-form.js')
@endsection

@section('content')

@php
  $existingRecipe = $menuItem->recipes->map(fn($r) => [
    'inventory_item_id' => $r->inventory_item_id,
    'name'              => $r->inventoryItem?->name ?? '',
    'unit'              => $r->inventoryItem?->unit ?? '',
    'quantity_required' => (float) $r->quantity_required,
  ])->values()->toArray();
@endphp

<script>
  window.inventoryItemsData = @json($inventoryData);
  window.existingRecipe     = @json($existingRecipe);
</script>

<form action="{{ route('recipes.update', $menuItem) }}" method="POST" id="recipe-form">
  @csrf
  @method('PUT')

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div>
      <h4 class="mb-1">Receta: {{ $menuItem->nombre }}</h4>
      <p class="mb-0 text-muted">{{ $menuItem->categoria }} · ${{ number_format($menuItem->precio, 2) }}</p>
    </div>
    <div class="d-flex gap-3">
      <a href="{{ route('recipes.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="icon-base ti tabler-check me-1"></i>Guardar Receta
      </button>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible mb-6">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Insumos de la Receta</h5>
    </div>
    <div class="card-body">
      <!-- Selector de insumo -->
      <div class="row g-3 align-items-end mb-5">
        <div class="col-md-7">
          <label class="form-label" for="select-insumo">Insumo</label>
          <select id="select-insumo" class="form-select" data-placeholder="Busca un insumo...">
            <option value="">Selecciona...</option>
            @foreach($inventoryItems as $inv)
            <option value="{{ $inv->id }}" data-nombre="{{ $inv->name }}" data-unit="{{ $inv->unit }}">
              {{ $inv->name }} ({{ \App\Models\InventoryItem::UNITS[$inv->unit] ?? $inv->unit }})
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="input-cantidad-receta">Cantidad requerida</label>
          <input type="number" id="input-cantidad-receta" class="form-control"
                 value="1" min="0.001" step="0.001" placeholder="0.000">
        </div>
        <div class="col-md-2">
          <button type="button" id="btn-agregar-insumo" class="btn btn-label-primary w-100">
            <i class="icon-base ti tabler-plus me-1"></i>Agregar
          </button>
        </div>
      </div>

      @error('recipe')
      <div class="alert alert-danger py-2">{{ $message }}</div>
      @enderror

      <div class="table-responsive">
        <table class="table table-bordered" id="recipe-table">
          <thead class="table-light">
            <tr>
              <th>Insumo</th>
              <th class="text-center" style="width:180px">Cantidad requerida</th>
              <th class="text-center" style="width:60px"></th>
            </tr>
          </thead>
          <tbody id="recipe-body">
            <tr id="recipe-empty-row">
              <td colspan="3" class="text-center text-muted py-4">
                <i class="icon-base ti tabler-clipboard-list icon-24px d-block mb-1"></i>
                Agrega insumos a la receta
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div id="recipe-hidden"></div>
    </div>
  </div>
</form>
@endsection
