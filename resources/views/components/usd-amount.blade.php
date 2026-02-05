@props(['amount', 'class' => ''])

@php
    $amountValue = (float) $amount;
@endphp

<span class="{{ $class }}">${{ number_format($amountValue, 2, '.', ',') }}</span>