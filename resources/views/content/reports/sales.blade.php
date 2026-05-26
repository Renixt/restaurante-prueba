@extends('layouts/layoutMaster')

@section('title', 'Reporte de Ventas - SGR')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/reports/report-filters.js')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-6 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">Reporte de Ventas</h4>
    <p class="mb-0"><a href="{{ route('reports.index') }}">Reportes</a> / Ventas</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('reports.sales', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-label-success">
      <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Excel
    </a>
    <a href="{{ route('reports.sales', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-label-danger">
      <i class="icon-base ti tabler-file-description me-1"></i> PDF
    </a>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Desde</label>
        <input type="date" name="from" class="form-control" value="{{ $from }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Hasta</label>
        <input type="date" name="to" class="form-control" value="{{ $to }}">
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-4 mb-6">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <span class="text-heading">Total Ventas</span>
        <h4 class="mb-0 mt-1">${{ number_format($total, 2) }}</h4>
        <small>{{ $orders->count() }} órdenes pagadas</small>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-datatable">
    <table class="table table-bordered table-hover report-table" id="salesTable">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Mesa</th>
          <th>Total</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
        <tr>
          <td>{{ $order->id }}</td>
          <td>{{ $order->mesa?->numero ?? '—' }}</td>
          <td>${{ number_format($order->total, 2) }}</td>
          <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
