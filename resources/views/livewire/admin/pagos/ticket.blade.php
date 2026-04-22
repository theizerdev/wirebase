<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Pago</title>
    <style>
        /*
         * Roccia RC-5801 — 47mm thermal
         * Ancho fijo 47mm en pantalla Y en impresión
         * para que la vista previa = impresión
         */
        @page {
            size: 47mm auto;
            margin: 0 !important;
            padding: 0 !important;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html {
            width: 47mm;
            margin: 0;
            padding: 0;
        }
        body {
            width: 47mm;
            max-width: 47mm;
            min-width: 47mm;
            margin: 0 auto;
            padding: 2mm 2mm;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: bold;
            color: #000;
            background: #fff;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            overflow-x: hidden;
        }
        .center { text-align: center; }
        .right  { text-align: right; }

        .text-title { font-size: 13px; font-weight: bold; }
        .text-big   { font-size: 11px; font-weight: bold; }
        .text-base  { font-size: 10px; font-weight: bold; }
        .text-sm    { font-size: 9px;  font-weight: bold; }
        .text-xs    { font-size: 8px;  font-weight: bold; }
        .mt-1 { margin-top: 2px; }
        .mt-2 { margin-top: 4px; }
        .mb-1 { margin-bottom: 2px; }

        .sep {
            width: 100%;
            text-align: center;
            font-size: 8px;
            margin: 2px 0;
            letter-spacing: -1px;
            overflow: hidden;
            white-space: nowrap;
            font-weight: bold;
        }
        .sep-bold {
            border-top: 2px solid #000;
            margin: 3px 0;
        }

        /* Key-Value rows */
        table.kv {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.kv td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 10px;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        table.kv .k {
            width: 38%;
            white-space: nowrap;
        }
        table.kv .v {
            width: 62%;
            text-align: right;
            word-break: break-word;
        }

        .stitle {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            margin: 2px 0;
        }

        /* Items */
        table.det {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 1px 0;
        }
        table.det th {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding: 1px 0;
        }
        table.det td {
            font-size: 9px;
            font-weight: bold;
            padding: 1px 0;
            vertical-align: top;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        table.det .d1 { width: 46%; text-align: left; }
        table.det .d2 { width: 14%; text-align: center; }
        table.det .d3 { width: 40%; text-align: right; }

        /* Grand total row */
        .gt td {
            font-size: 12px !important;
            font-weight: bold !important;
            padding-top: 3px !important;
        }

        .cuota-box {
            border: 1px dashed #000;
            padding: 2px 3px;
            margin: 3px 0;
        }

        .qr-area { text-align: center; margin: 3px 0; }
        .qr-area img { max-width: 80px; height: auto; }

        .badge {
            display: inline-block;
            padding: 0 4px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
        }

        /* PRINT: idéntico a pantalla, sin escalado del navegador */
        @media print {
            @page {
                size: 47mm auto;
                margin: 0mm !important;
                padding: 0mm !important;
            }
            html, body {
                width: 47mm !important;
                max-width: 47mm !important;
                min-width: 47mm !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-transform: none !important;
                transform: none !important;
                zoom: 1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                padding: 1mm 1.5mm !important;
            }
            /* Ocultar encabezados/pies del navegador */
            title { display: none; }
        }
    </style>
    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        }
    </script>
    <style>
        /* Fix: quitar headers/footers que el navegador agrega al imprimir */
        @media print {
            html { overflow: visible !important; }
            * { overflow: visible !important; }
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="center">
        <div class="text-title">{{ $merchant['name'] }}</div>
        @if($merchant['rif'])
            <div class="text-sm">RIF: {{ $merchant['rif'] }}</div>
        @endif
        @if($merchant['branch'])
            <div class="text-sm mt-1">{{ $merchant['branch'] }}</div>
        @endif
        @if($merchant['address'])
            <div class="text-xs">{{ $merchant['address'] }}</div>
        @endif
        @if($merchant['branch_phone'] ?? $merchant['phone'])
            <div class="text-xs">Tel: {{ $merchant['branch_phone'] ?: $merchant['phone'] }}</div>
        @endif
    </div>

    <div class="sep-bold"></div>

    {{-- TRANSACTION --}}
    <div class="center mb-1">
        <div class="text-big">RECIBO DE PAGO</div>
        <div class="text-sm">No. {{ $payment['transaction'] }}</div>
    </div>

    <table class="kv">
        <tr>
            <td class="k">Fecha:</td>
            <td class="v">{{ $payment['date'] }}</td>
        </tr>
        <tr>
            <td class="k">Hora:</td>
            <td class="v">{{ $payment['time'] }}</td>
        </tr>
        <tr>
            <td class="k">Estado:</td>
            <td class="v"><span class="badge">{{ $payment['status'] }}</span></td>
        </tr>
        @if($payment['cashier'])
        <tr>
            <td class="k">Cajero:</td>
            <td class="v">{{ \Illuminate\Support\Str::limit($payment['cashier'], 16) }}</td>
        </tr>
        @endif
    </table>

    <div class="sep">--------------------------------</div>

    {{-- CUSTOMER --}}
    <div class="stitle">— Cliente —</div>
    <table class="kv">
        <tr>
            <td class="k">Nombre:</td>
            <td class="v">{{ \Illuminate\Support\Str::limit($customer['name'], 16) }}</td>
        </tr>
        <tr>
            <td class="k">Doc:</td>
            <td class="v">{{ $customer['document'] }}</td>
        </tr>
        @if($customer['phone'])
        <tr>
            <td class="k">Tel:</td>
            <td class="v">{{ $customer['phone'] }}</td>
        </tr>
        @endif
    </table>

    <div class="sep">--------------------------------</div>

    {{-- DETAIL --}}
    <div class="stitle">— Detalle —</div>
    <table class="det">
        <thead>
            <tr>
                <th class="d1">Concepto</th>
                <th class="d2">Qty</th>
                <th class="d3">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
            <tr>
                <td class="d1">{{ \Illuminate\Support\Str::limit($item['description'], 12) }}</td>
                <td class="d2">{{ number_format($item['qty'], 0) }}</td>
                <td class="d3">${{ number_format($item['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sep">--------------------------------</div>

    {{-- PAYMENT METHOD --}}
    <div class="stitle">— Pago —</div>
    @if($payment['is_mixed'] && !empty($payment['mixed_details']))
        <div class="text-sm center mb-1">PAGO MIXTO</div>
        <table class="kv">
        @foreach($payment['mixed_details'] as $mix)
            <tr>
                <td class="k">{{ \Illuminate\Support\Str::limit(ucfirst(str_replace('_', ' ', $mix['metodo'] ?? '')), 10) }}</td>
                <td class="v">${{ number_format($mix['monto'] ?? 0, 2) }}</td>
            </tr>
            @if(!empty($mix['referencia']))
            <tr>
                <td class="k text-xs">Ref:</td>
                <td class="v text-xs">{{ $mix['referencia'] }}</td>
            </tr>
            @endif
        @endforeach
        </table>
    @else
        <table class="kv">
            <tr>
                <td class="k">Metodo:</td>
                <td class="v">{{ $payment['method'] }}</td>
            </tr>
            @if($payment['reference'])
            <tr>
                <td class="k">Ref:</td>
                <td class="v">{{ $payment['reference'] }}</td>
            </tr>
            @endif
        </table>
    @endif

    <div class="sep-bold"></div>

    {{-- TOTALS --}}
    <table class="kv">
        <tr>
            <td class="k">Subtotal:</td>
            <td class="v">${{ number_format($totals['subtotal_usd'], 2) }}</td>
        </tr>
        @if($totals['discount_usd'] > 0)
        <tr>
            <td class="k">Desc:</td>
            <td class="v">-${{ number_format($totals['discount_usd'], 2) }}</td>
        </tr>
        @endif
        <tr class="gt">
            <td class="k">TOTAL $:</td>
            <td class="v">${{ number_format($totals['total_usd'], 2) }}</td>
        </tr>
    </table>

    @if($totals['exchange_rate'])
        <div class="sep">................................</div>
        <table class="kv">
            <tr>
                <td class="k text-sm">Tasa BCV:</td>
                <td class="v text-sm">{{ number_format($totals['exchange_rate'], 4) }}</td>
            </tr>
            @if($totals['subtotal_bs'])
            <tr>
                <td class="k">Subt Bs:</td>
                <td class="v">{{ number_format($totals['subtotal_bs'], 2) }}</td>
            </tr>
            @endif
            @if($totals['discount_bs'] > 0)
            <tr>
                <td class="k">Desc Bs:</td>
                <td class="v">-{{ number_format($totals['discount_bs'], 2) }}</td>
            </tr>
            @endif
            <tr class="gt">
                <td class="k">TOTAL Bs:</td>
                <td class="v">{{ number_format($totals['total_bs'], 2) }}</td>
            </tr>
        </table>
    @elseif($totals['total_bs'])
        <div class="sep">................................</div>
        <table class="kv">
            <tr class="gt">
                <td class="k">TOTAL Bs:</td>
                <td class="v">{{ number_format($totals['total_bs'], 2) }}</td>
            </tr>
        </table>
    @endif

    {{-- NEXT INSTALLMENT --}}
    @if($nextInstallment)
        <div class="sep">--------------------------------</div>
        <div class="cuota-box">
            <div class="stitle">Proxima Cuota</div>
            <table class="kv">
                <tr>
                    <td class="k">Cuota:</td>
                    <td class="v">{{ $nextInstallment['number'] }}</td>
                </tr>
                <tr>
                    <td class="k">Vence:</td>
                    <td class="v">{{ $nextInstallment['date'] }}</td>
                </tr>
                <tr>
                    <td class="k">Monto:</td>
                    <td class="v">${{ number_format($nextInstallment['amount_usd'], 2) }}</td>
                </tr>
                @if($nextInstallment['amount_bs'])
                @endif
                @if($nextInstallment['pending'] != $nextInstallment['amount_usd'])
                <tr>
                    <td class="k">Pend.:</td>
                    <td class="v">${{ number_format($nextInstallment['pending'], 2) }}</td>
                </tr>
                @endif
            </table>
            @if($nextInstallment['amount_bs'])
            <div class="text-xs center mt-1">*Tasa ref. del dia</div>
            @endif
        </div>
    @endif

    <div class="sep">--------------------------------</div>

    {{-- QR --}}
    @if(!empty($qrBase64))
    <div class="qr-area">
        <div class="text-xs">Escanea para verificar</div>
        <img src="data:image/png;base64,{{ $qrBase64 }}" width="80" height="80" alt="QR">
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="sep">--------------------------------</div>
    <div class="center text-xs mt-1">Comprobante de pago</div>
    <div class="center text-big mt-1">Gracias!</div>
    <div class="center text-xs mt-1">{{ $merchant['name'] }}</div>
    <div class="center text-xs mt-1">{{ now()->format('d/m/Y H:i') }}</div>

</body>
</html>
