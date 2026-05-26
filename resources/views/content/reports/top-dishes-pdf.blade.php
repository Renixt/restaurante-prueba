<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Platillos más vendidos</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    h1 { font-size: 18px; margin-bottom: 4px; }
    p { margin: 0 0 16px; color: #666; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th { background: #696cff; color: #fff; padding: 8px; text-align: left; }
    td { border-bottom: 1px solid #eee; padding: 6px 8px; }
  </style>
</head>
<body>
  <h1>Platillos más vendidos</h1>
  <p>Periodo: {{ $from }} — {{ $to }}</p>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Platillo</th>
        <th>Cantidad</th>
        <th>Ingresos</th>
      </tr>
    </thead>
    <tbody>
      @foreach($dishes as $i => $dish)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $dish->name }}</td>
        <td>{{ $dish->total_qty }}</td>
        <td>${{ number_format($dish->total_revenue, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
