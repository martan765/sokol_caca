@props(['date'])

{{-- Kolik zbývá do akce: Dnes / Zítra / za 3 dny / za 12 dní --}}
@php
    $days  = (int) now()->startOfDay()->diffInDays($date->copy()->startOfDay());
    $label = match (true) {
        $days === 0 => 'Dnes',
        $days === 1 => 'Zítra',
        $days < 5   => "za {$days} dny",
        default     => "za {$days} dní",
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
    'bg-amber-100 text-amber-800' => $days <= 1,
    'bg-gray-100 text-gray-600' => $days > 1,
]) }}>{{ $label }}</span>
