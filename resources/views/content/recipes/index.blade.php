@extends('layouts/layoutMaster')

@section('title', 'Recetas - Platillos')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-6">
  <div>
    <h4 class="mb-1">Recetas de Platillos</h4>
    <p class="mb-0 text-muted">Define los insumos que requiere cada platillo del menú</p>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Platillo</th>
          <th>Categoría</th>
          <th class="text-center">Insumos</th>
          <th class="text-center">Receta</th>
          <th class="text-center">Acción</th>
        </tr>
      </thead>
      <tbody>
        @foreach($menuItems as $item)
        <tr>
          <td class="fw-medium">{{ $item->nombre }}</td>
          <td class="text-muted">{{ $item->categoria }}</td>
          <td class="text-center">{{ $item->recipes->count() }}</td>
          <td class="text-center">
            @if($item->recipes->count() > 0)
              <span class="badge bg-label-success">Con receta</span>
            @else
              <span class="badge bg-label-danger">Sin receta</span>
            @endif
          </td>
          <td class="text-center">
            <a href="{{ route('recipes.edit', $item) }}"
               class="btn btn-sm btn-label-primary">
              <i class="icon-base ti tabler-pencil me-1"></i>Gestionar
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
