@extends('layouts/layoutMaster')

@section('title', 'Nueva Orden - SGR')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/orders/order-form.js')
@endsection

@section('content')
  @php
    $menuItemsData = $menuItems->keyBy('id')->map(function ($i) {
        return [
            'id' => $i->id,
            'nombre' => $i->nombre,
            'categoria' => $i->categoria,
            'precio' => (float) $i->precio,
        ];
    });
  @endphp

  <script>
    window.menuItemsData = @json($menuItemsData);
  </script>

  <form action="{{ route('orders.store') }}" method="POST" id="order-form">
    @csrf

    <div
      class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
      <div>
        <h4 class="mb-1">Nueva Orden</h4>
        <p class="mb-0 text-muted">Completa los datos para crear la orden</p>
      </div>
      <div class="d-flex gap-3">
        <a href="{{ route('orders.index') }}" class="btn btn-label-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary" id="btn-submit-order">
          <i class="icon-base ti tabler-check me-1"></i>Crear Orden
        </button>
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible mb-6" role="alert">
        <strong>Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row">
      <!-- Columna principal -->
      <div class="col-12 col-lg-8">

        <!-- Tipo de orden -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Tipo de Orden</h5>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-6">
                <div class="form-check form-check-card h-100">
                  <input class="form-check-input" type="radio" name="type" id="type-mesa" value="mesa"
                    {{ old('type', 'mesa') === 'mesa' ? 'checked' : '' }}>
                  <label class="form-check-label card h-100 cursor-pointer" for="type-mesa">
                    <div class="card-body text-center py-4">
                      <i class="icon-base ti tabler-armchair icon-36px d-block mb-2 text-primary"></i>
                      <span class="fw-medium">Mesa</span>
                    </div>
                  </label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-check form-check-card h-100">
                  <input class="form-check-input" type="radio" name="type" id="type-takeaway" value="takeaway"
                    {{ old('type') === 'takeaway' ? 'checked' : '' }}>
                  <label class="form-check-label card h-100 cursor-pointer" for="type-takeaway">
                    <div class="card-body text-center py-4">
                      <i class="icon-base ti tabler-shopping-bag icon-36px d-block mb-2 text-warning"></i>
                      <span class="fw-medium">Para llevar</span>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Selección de mesa (condicional) -->
            <div class="mt-5" id="mesa-section">
              <label class="form-label" for="mesa_id">Mesa <span class="text-danger">*</span></label>
              <select id="mesa_id" name="mesa_id" class="select2 form-select @error('mesa_id') is-invalid @enderror"
                data-placeholder="Selecciona una mesa">
                <option value="">Selecciona una mesa</option>
                @foreach ($mesas as $mesa)
                  <option value="{{ $mesa->id }}" {{ old('mesa_id') == $mesa->id ? 'selected' : '' }}>
                    Mesa {{ $mesa->numero }} (cap. {{ $mesa->capacidad }})
                  </option>
                @endforeach
              </select>
              @error('mesa_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <!-- /Tipo de orden -->

        <!-- Agregar platillos -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Platillos</h5>
          </div>
          <div class="card-body">
            <!-- Selector de platillo -->
            <div class="row g-3 align-items-end mb-5">
              <div class="col-md-7">
                <label class="form-label" for="select-platillo">Platillo</label>
                <select id="select-platillo" class="select2 form-select"
                  data-placeholder="Busca y selecciona un platillo">
                  <option value="">Busca un platillo...</option>
                  @foreach ($menuItems as $item)
                    <option value="{{ $item->id }}" data-precio="{{ $item->precio }}"
                      data-nombre="{{ $item->nombre }}" data-categoria="{{ $item->categoria }}">
                      {{ $item->nombre }} — ${{ number_format($item->precio, 2) }} ({{ $item->categoria }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label" for="input-cantidad">Cantidad</label>
                <input type="number" id="input-cantidad" class="form-control" value="1" min="1"
                  max="99">
              </div>
              <div class="col-md-3">
                <button type="button" id="btn-agregar-item" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-plus me-1"></i>Agregar
                </button>
              </div>
            </div>

            @error('items')
              <div class="alert alert-danger py-2">{{ $message }}</div>
            @enderror

            <!-- Tabla de items -->
            <div class="table-responsive">
              <table class="table table-bordered" id="items-table">
                <thead class="table-light">
                  <tr>
                    <th>Platillo</th>
                    <th class="text-center" style="width:100px">Precio</th>
                    <th class="text-center" style="width:90px">Cant.</th>
                    <th class="text-center" style="width:110px">Subtotal</th>
                    <th class="text-center" style="width:60px"></th>
                  </tr>
                </thead>
                <tbody id="items-body">
                  <tr id="empty-row">
                    <td colspan="5" class="text-center text-muted py-4">
                      <i class="icon-base ti tabler-shopping-cart icon-24px d-block mb-1"></i>
                      Agrega platillos a la orden
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            {{-- Inputs ocultos generados por JS --}}
            <div id="items-hidden"></div>
          </div>
        </div>
        <!-- /Agregar platillos -->

        <!-- Notas -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Notas</h5>
          </div>
          <div class="card-body">
            <textarea name="notes" class="form-control" rows="3"
              placeholder="Indicaciones especiales, alergias, modificaciones...">{{ old('notes') }}</textarea>
          </div>
        </div>

      </div>
      <!-- /Columna principal -->

      <!-- Columna lateral -->
      <div class="col-12 col-lg-4">

        <!-- Resumen -->
        <div class="card mb-6" id="summary-card">
          <div class="card-header">
            <h5 class="card-title mb-0">Resumen</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <span class="text-muted">Platillos</span>
              <span id="summary-count" class="fw-medium">0</span>
            </div>
            <div class="d-flex justify-content-between mb-4 border-top pt-3">
              <span class="fw-bold">Total</span>
              <span id="summary-total" class="fw-bold text-primary fs-5">$0.00</span>
            </div>

            <!-- Dividir cuenta -->
            <div class="mb-4">
              <label class="form-label" for="split_count">Dividir en (personas)</label>
              <div class="input-group">
                <span class="input-group-text"><i class="icon-base ti tabler-users"></i></span>
                <input type="number" name="split_count" id="split_count" class="form-control"
                  value="{{ old('split_count', 1) }}" min="1" max="20">
              </div>
              <div class="text-muted small mt-1">
                Por persona: <strong id="per-person">$0.00</strong>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="btn-submit-order-2">
              <i class="icon-base ti tabler-check me-1"></i>Confirmar Orden
            </button>
          </div>
        </div>

        <!-- Pago (opcional al crear) -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title mb-0">Pago (opcional)</h5>
          </div>
          <div class="card-body">
            <div class="ecommerce-select2-dropdown">
              <label class="form-label mb-1" for="payment_method">Método de pago</label>
              <select id="payment_method" name="payment_method" class="select2 form-select"
                data-placeholder="Selecciona (opcional)">
                <option value="">Sin registrar</option>
                <option value="efectivo" {{ old('payment_method') === 'efectivo' ? 'selected' : '' }}>Efectivo
                </option>
                <option value="tarjeta" {{ old('payment_method') === 'tarjeta' ? 'selected' : '' }}>Tarjeta
                </option>
                <option value="transferencia"{{ old('payment_method') === 'transferencia' ? 'selected' : '' }}>
                  Transferencia</option>
              </select>
            </div>
          </div>
        </div>

      </div>
      <!-- /Columna lateral -->
    </div>
  </form>
@endsection
