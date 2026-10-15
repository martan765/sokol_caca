@props(['type'])

{{-- Trénink = klubová zelená, zápas = tmavý štítek jako výsledková tabule --}}
<span {{ $attributes->class([
    'inline-block px-2 py-0.5 rounded-full text-xs font-bold',
    'bg-brand-100 text-brand-800' => $type === 'training',
    'bg-gray-900 text-white' => $type === 'game',
]) }}>{{ $type === 'training' ? 'Trénink' : 'Zápas' }}</span>
