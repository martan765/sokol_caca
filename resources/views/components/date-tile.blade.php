@props(['date'])

{{-- Kalendářní dlaždice: den v týdnu, číslo dne, měsíc --}}
@php $date = $date->copy()->locale('cs'); @endphp

<div {{ $attributes->class('shrink-0 w-14 rounded-lg bg-brand-50 border border-brand-100 text-center py-1.5') }}>
    <div class="text-[10px] font-bold text-brand-700 uppercase tracking-widest">{{ $date->translatedFormat('D') }}</div>
    <div class="text-xl font-black text-gray-900 leading-tight">{{ $date->format('j') }}</div>
    <div class="text-[10px] font-semibold text-gray-500 uppercase">{{ $date->translatedFormat('M') }}</div>
</div>
