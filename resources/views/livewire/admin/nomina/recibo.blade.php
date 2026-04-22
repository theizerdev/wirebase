<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Nómina</title>
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
    <h2>Recibo de Nómina</h2>
    <div class="small">Periodo: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</div>
    <div class="mt-2">
        <strong>Empleado:</strong> {{ $empleado->nombre }} {{ $empleado->apellido }} ({{ $empleado->documento }})
    </div>
    <div class="mt-2">
        <strong>Puesto:</strong> {{ $empleado->puesto ?? 'N/A' }} • <strong>Método:</strong> {{ ucfirst($empleado->metodo_pago ?? 'N/A') }}
    </div>

    @php
        $percepciones = 0; $deducciones = 0;
        foreach ($items as $it) {
            if ($it['tipo'] === 'percepcion') $percepciones += $it['subtotal'];
            else $deducciones += $it['subtotal'];
        }
        $neto = $percepciones - $deducciones;
    @endphp

    <h3 class="mt-2">Detalle</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Tipo</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
            <tr>
                <td>{{ $it['concepto_nombre'] }}</td>
                <td>{{ ucfirst($it['tipo']) }}</td>
                <td class="text-right">${{ number_format($it['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Percepciones</th>
                <th class="text-right">${{ number_format($percepciones, 2) }}</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">Deducciones</th>
                <th class="text-right">${{ number_format($deducciones, 2) }}</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">Neto a Pagar</th>
                <th class="text-right">${{ number_format($neto, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 12px; display: flex; align-items: center; gap: 16px;">
        @if(!empty($qrBase64))
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Nómina" width="120" height="120">
        @endif
        <div>
            <p class="small">Firma digital del periodo: {{ substr($signature ?? '', 0, 16) }}...</p>
            <p class="small">Verificación: EMP {{ $empleado->id }}, Periodo {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>
        </div>
    </div>
    <p class="small">Este recibo es generado electrónicamente por el sistema de nómina.</p>
</body>
</html>
