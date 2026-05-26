<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte de Ventas</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    h1 { font-size: 18px; margin-bottom: 4px; }
    p { margin: 0 0 16px; color: #666; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th { background: #696cff; color: #fff; padding: 8px; text-align: left; }
    td { border-bottom: 1px solid #eee; padding: 6px 8px; }
    .total { font-weight: bold; font-size: 14px; margin-top: 12px; }
  </style>
</head>
<body>
  <h1>Reporte de Ventas</h1>
  <p>Periodo: {{ $from }} — {{ $to }}</p>

  <table>
    <thead>
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

  <p class="total" style="margin-top:16px;">Total: ${{ number_format($total, 2) }} ({{ $orders->count() }} órdenes)</p>
</body>
</html>
