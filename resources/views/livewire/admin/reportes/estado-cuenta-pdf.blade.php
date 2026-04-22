<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .mt-2 { margin-top: 12px; }
        .mb-2 { margin-bottom: 12px; }
        .small { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h2>Estado de Cuenta</h2>
    <div class="small">Generado: {{ $generatedAt }}</div>
    <div class="mt-2">
        <strong>Cliente:</strong> {{ $cliente?->nombre }} {{ $cliente?->apellido }} ({{ $cliente?->documento }})
    </div>
    <div class="mt-2">
        <strong>Total Pagado:</strong> ${{ number_format($result['resumen']['total_pagado'] ?? 0, 2) }} |
        <strong>Saldo Pendiente:</strong> ${{ number_format($result['resumen']['pendiente'] ?? 0, 2) }} |
        <strong>Próximo Vencimiento:</strong> {{ $result['resumen']['proximo_vencimiento'] ?? 'N/A' }}
    </div>
    
    <h3 class="mt-2">Contratos</h3>
    <table>
        <thead>
            <tr>
                <th>N° Contrato</th>
                <th>Unidad</th>
                <th>Inicio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($result['contratos'] as $c)
            <tr>
                <td>{{ $c['numero_contrato'] ?? $c['id'] }}</td>
                <td>{{ $c['unidad']['moto']['marca'] ?? '' }} {{ $c['unidad']['moto']['modelo'] ?? '' }} ({{ $c['unidad']['moto']['anio'] ?? '' }})</td>
                <td>{{ \Carbon\Carbon::parse($c['created_at'])->format('d/m/Y') }}</td>
                <td>{{ ucfirst($c['estado'] ?? 'borrador') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="small">Sin contratos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="mt-2">Cuotas Pagadas</h3>
    <table>
        <thead>
            <tr>
                <th>Contrato</th>
                <th>Descripción</th>
                <th>Fecha Pago</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($result['cuotas_pagadas'] as $c)
            <tr>
                <td>#{{ $c['contrato_id'] }}</td>
                <td>{{ $c['descripcion'] }}</td>
                <td>{{ $c['fecha_pago'] }}</td>
                <td class="text-right">${{ number_format($c['monto'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="small">Sin cuotas pagadas</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="mt-2">Cuotas Pendientes</h3>
    <table>
        <thead>
            <tr>
                <th>Contrato</th>
                <th># Cuota</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($result['cuotas_pendientes'] as $c)
            <tr>
                <td>#{{ $c['contrato_id'] }}</td>
                <td>{{ $c['numero'] }}</td>
                <td>{{ $c['vencimiento'] }}</td>
                <td>{{ ucfirst($c['estado']) }}</td>
                <td class="text-right">${{ number_format($c['saldo'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="small">Sin cuotas pendientes</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="mt-2">Pagos</h3>
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Fecha</th>
                <th class="text-right">Total</th>
                <th>Método</th>
                <th>Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($result['pagos'] as $p)
            <tr>
                <td>{{ $p['numero_completo'] ?? '' }}</td>
                <td>{{ $p['fecha'] ?? '' }}</td>
                <td class="text-right">${{ number_format($p['total'] ?? 0, 2) }}</td>
                <td>{{ ucfirst($p['metodo_pago'] ?? '') }}</td>
                <td>{{ $p['referencia'] ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="small">Sin pagos registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
