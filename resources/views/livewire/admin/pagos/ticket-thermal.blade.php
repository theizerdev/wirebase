<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Ticket</title>
<style>
@page { size: 45mm auto; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    width: 45mm;
    max-width: 45mm;
    overflow: hidden;
    margin: 0;
    padding: 0 0 2mm 0;
    font-family: 'Lucida Console', 'Consolas', monospace;
    font-size: 9px;
    font-weight: 900;
    -webkit-text-stroke: 0.3px #000;
    line-height: 1.3;
    color: #000;
}
div { word-wrap: break-word; }
.c { text-align: center; }
.r { text-align: right; }
.s { font-size: 8px; }
.big { font-size: 11px; }
hr { border: none; border-top: 1px dashed #000; margin: 1px 0; }
hr.solid { border-top: 1px solid #000; }
.total-box { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 1px 0; margin: 1px 0; font-size: 12px; }
.cuota { border: 1px dashed #000; padding: 1px 2px; margin: 2px 0; }
@media print {
    @page { size: 45mm auto; margin: 0 !important; }
    html, body { width: 45mm !important; max-width: 45mm !important; margin: 0 !important; padding: 0 0 2mm 0 !important; }
}
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<div class="c big">{{ mb_strtoupper($merchant['name']) }}</div>
@if($merchant['rif'])<div class="c s">RIF: {{ $merchant['rif'] }}</div>@endif
@if($merchant['branch'])<div class="c s">{{ $merchant['branch'] }}</div>@endif
@if($merchant['branch_phone'] ?? $merchant['phone'])<div class="c s">Tel: {{ $merchant['branch_phone'] ?: $merchant['phone'] }}</div>@endif
<hr class="solid">

{{-- TRANSACCION --}}
<div class="c big">{{ mb_strtoupper('RECIBO DE PAGO') }}</div>
<div class="c s">No. {{ $payment['transaction'] }}</div>
@if($contractNumber)<div class="c s">Contrato: {{ $contractNumber }}</div>@endif <br>
<div class="s">{{ $payment['date'] }} {{ $payment['time'] }} | {{ $payment['status'] }}</div>
@if($payment['cashier'])<div class="small">Cajero: {{ Str::limit($payment['cashier'], 18) }}</div>@endif

<hr>

{{-- CLIENTE --}}
<div>{{ Str::limit($customer['name'], 24) }}</div>
<div class="s">{{ $customer['document'] }}</div>
@if($customer['phone'])<div class="s">Tel: {{ $customer['phone'] }}</div>@endif
<hr>

{{-- ITEMS --}}
@foreach($details as $item)
<div>{{ Str::limit($item['description'], 24) }}</div>
<div class="r">{{ number_format($item['qty'], 0) }}x ${{ number_format($item['price'], 2) }} = ${{ number_format($item['subtotal'], 2) }}</div>
@endforeach
<hr>

{{-- PAGO --}}
@if($payment['is_mixed'] && !empty($payment['mixed_details']))
@foreach($payment['mixed_details'] as $mix)
<div class="s">{{ ucfirst(str_replace('_', ' ', $mix['metodo'] ?? '')) }}: ${{ number_format($mix['monto'] ?? 0, 2) }}</div>
@if(!empty($mix['referencia']))<div class="s">Ref: {{ $mix['referencia'] }}</div>@endif
@endforeach
@else
<div class="s">{{ $payment['method'] }}@if($payment['reference']) | Ref: {{ $payment['reference'] }}@endif</div>
@endif

{{-- TOTALES --}}
@if($totals['discount_usd'] > 0)
<div class="s">Subtotal: ${{ number_format($totals['subtotal_usd'], 2) }}</div>
<div class="s">Desc: -${{ number_format($totals['discount_usd'], 2) }}</div>
@endif
<div class="total-box r">TOTAL ${{ number_format($totals['total_usd'], 2) }}</div>

@if($totals['total_bs'])
<div class="r">Bs. {{ number_format($totals['total_bs'], 2) }}</div>
@if($totals['exchange_rate'])<div class="s">Tasa: {{ number_format($totals['exchange_rate'], 2) }}</div>@endif
@endif

{{-- PROXIMA CUOTA --}}
@if($nextInstallment)
<div class="cuota">
<div class="s">Prox. Cuota #{{ $nextInstallment['number'] }}</div>
<div class="s">Vence: {{ $nextInstallment['date'] }}</div>
<div class="s">Monto: ${{ number_format($nextInstallment['amount_usd'], 2) }}@if($nextInstallment['pending'] != $nextInstallment['amount_usd']) | Pend: ${{ number_format($nextInstallment['pending'], 2) }}@endif</div>
</div>
@endif

{{-- QR --}}
@if(!empty($qrBase64))
<div class="c"><img src="data:image/png;base64,{{ $qrBase64 }}" width="50" height="50" alt="QR"></div>
@endif

{{-- PIE --}}
<hr>
<div class="c s">Gracias por su pago</div>
<div class="c s">{{ now()->format('d/m/Y H:i') }}</div>
<br><br>

<script>window.onload=function(){setTimeout(function(){window.print()},500)};</script>
</body>
</html>
