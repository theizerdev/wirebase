<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard - Resumen</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2, h3 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .small { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h2>Dashboard - Resumen Ejecutivo</h2>
    <div class="small">Generado: {{ $generatedAt }}</div>
    <div class="small">Clientes en sistema: {{ $clienteCount }}</div>

    <h3>Métricas</h3>
    <table>
        <thead>
            <tr>
                <th>Métrica</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Clientes Totales</td><td>{{ $metrics['clientes']['total'] ?? 0 }}</td></tr>
            <tr><td>Clientes Activos</td><td>{{ $metrics['clientes']['activos'] ?? 0 }}</td></tr>
            <tr><td>Clientes Inactivos</td><td>{{ $metrics['clientes']['inactivos'] ?? 0 }}</td></tr>
            <tr><td>Contratos Vigentes</td><td>{{ $metrics['contratos']['vigentes'] ?? 0 }}</td></tr>
            <tr><td>Contratos Vencidos</td><td>{{ $metrics['contratos']['vencidos'] ?? 0 }}</td></tr>
            <tr><td>Pagos Recibidos</td><td>{{ $metrics['pagos']['recibidos'] ?? 0 }}</td></tr>
            <tr><td>Pagos Pendientes</td><td>{{ $metrics['pagos']['pendientes'] ?? 0 }}</td></tr>
            <tr><td>Pagos Morosos</td><td>{{ $metrics['pagos']['morosos'] ?? 0 }}</td></tr>
            <tr><td>Ingresos Mensuales</td><td>${{ number_format($metrics['ingresos']['mensual'] ?? 0, 2) }}</td></tr>
            <tr><td>Ingresos Anuales</td><td>${{ number_format($metrics['ingresos']['anual'] ?? 0, 2) }}</td></tr>
        </tbody>
    </table>

    <p class="small">Este documento resume el estado del sistema a la fecha indicada.</p>
</body>
</html>
