@props(['amount' => 0, 'currency' => 'EGP', 'class' => ''])
@php
    $amt = (float) $amount;
    $sign = $amt < 0 ? '-' : '';
    $abs = number_format(abs($amt), 2);
    $sym = match (strtoupper($currency)) { 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'EGP' => 'EGP ', default => $currency . ' ' };
@endphp
<span class="{{ $class }}">{{ $sign }}{{ $sym }}{{ $abs }}</span>
