@php
    // WMO kód počasí -> [popis, ikona]
    $describe = fn (?int $code, bool $isDay = true) => match (true) {
        $code === 0                                            => ['Jasno', $isDay ? '☀️' : '🌙'],
        in_array($code, [1, 2], true)                          => ['Polojasno', $isDay ? '🌤️' : '☁️'],
        $code === 3                                            => ['Zataženo', '☁️'],
        in_array($code, [45, 48], true)                        => ['Mlha', '🌫️'],
        in_array($code, [51, 53, 55, 56, 57], true)            => ['Mrholení', '🌦️'],
        in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => ['Déšť', '🌧️'],
        in_array($code, [71, 73, 75, 77, 85, 86], true)        => ['Sněžení', '🌨️'],
        in_array($code, [95, 96, 99], true)                    => ['Bouřka', '⛈️'],
        default                                                => ['Neznámé podmínky', '🌡️'],
    };
    $fmt = fn ($value, int $decimals = 0) => $value === null ? '—' : number_format($value, $decimals, ',', ' ');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Počasí na hřišti
        </h2>
    </x-slot>

    <div class="py-8 px-4">
        <div class="max-w-7xl mx-auto space-y-6">
            @if(!empty($weather['current']))
                @php
                    $current = $weather['current'];
                    $daily   = $weather['daily'] ?? [];
                    $now     = \Carbon\Carbon::parse($current['time'] ?? now());
                    $sunrise = isset($daily['sunrise'][0]) ? \Carbon\Carbon::parse($daily['sunrise'][0]) : null;
                    $sunset  = isset($daily['sunset'][0]) ? \Carbon\Carbon::parse($daily['sunset'][0]) : null;
                    $isDay   = ! $sunrise || ! $sunset || $now->between($sunrise, $sunset);
                    [$label, $icon] = $describe($current['weather_code'] ?? null, $isDay);
                @endphp

                <!-- Aktuální podmínky -->
                <div class="rounded-2xl shadow-sm overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-600 text-white">
                    <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest text-brand-200">Částkov · teď</div>
                            <div class="mt-3 flex items-center gap-5">
                                <span class="text-6xl sm:text-7xl leading-none" aria-hidden="true">{{ $icon }}</span>
                                <div>
                                    <div class="text-5xl sm:text-6xl font-black leading-none">
                                        {{ $fmt($current['temperature_2m'] ?? null, 1) }}<span class="text-2xl font-bold text-brand-200 ml-1">°C</span>
                                    </div>
                                    <div class="mt-2 text-lg font-semibold">{{ $label }}</div>
                                    <div class="text-sm text-brand-100">Pocitově {{ $fmt($current['apparent_temperature'] ?? null, 1) }} °C</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white/10 ring-1 ring-inset ring-white/15 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-brand-200">Vítr</div>
                                <div class="mt-1 text-2xl font-black">{{ $fmt($current['wind_speed_10m'] ?? null, 1) }} <span class="text-sm font-bold text-brand-200">km/h</span></div>
                            </div>
                            <div class="rounded-xl bg-white/10 ring-1 ring-inset ring-white/15 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-brand-200">Srážky teď</div>
                                <div class="mt-1 text-2xl font-black">{{ $fmt($current['precipitation'] ?? null, 1) }} <span class="text-sm font-bold text-brand-200">mm</span></div>
                            </div>
                            <div class="rounded-xl bg-white/10 ring-1 ring-inset ring-white/15 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-brand-200">Východ slunce</div>
                                <div class="mt-1 text-2xl font-black">{{ $sunrise?->format('H:i') ?? '—' }}</div>
                            </div>
                            <div class="rounded-xl bg-white/10 ring-1 ring-inset ring-white/15 p-4">
                                <div class="text-xs font-bold uppercase tracking-wider text-brand-200">Západ slunce</div>
                                <div class="mt-1 text-2xl font-black">{{ $sunset?->format('H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Předpověď na 7 dní -->
                @if(!empty($daily['time']))
                    <div>
                        <h3 class="flex items-center gap-2 text-base font-bold text-gray-700 mb-3 px-1 uppercase tracking-wider">
                            <span class="w-1.5 h-5 rounded-full bg-brand-600"></span>
                            Předpověď na týden
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                            @foreach($daily['time'] as $i => $day)
                                @php
                                    $date     = \Carbon\Carbon::parse($day)->locale('cs');
                                    $dayName  = $date->isToday() ? 'Dnes' : ($date->isTomorrow() ? 'Zítra' : $date->translatedFormat('l'));
                                    $rainProb = $daily['precipitation_probability_max'][$i] ?? null;
                                    $rainy    = $rainProb !== null && $rainProb >= 60;
                                    [$dayLabel, $dayIcon] = $describe($daily['weather_code'][$i] ?? null);
                                @endphp
                                <div @class([
                                    'rounded-xl border p-4 text-center shadow-sm',
                                    'bg-white border-gray-200' => ! $rainy,
                                    'bg-sky-50 border-sky-200' => $rainy,
                                    'ring-2 ring-brand-500' => $date->isToday(),
                                ])>
                                    <div class="text-sm font-bold text-gray-800 capitalize">{{ $dayName }}</div>
                                    <div class="text-xs text-gray-400">{{ $date->format('d.m.') }}</div>
                                    <div class="my-3 text-4xl leading-none" title="{{ $dayLabel }}" aria-hidden="true">{{ $dayIcon }}</div>
                                    <div class="text-xs text-gray-500 mb-2">{{ $dayLabel }}</div>
                                    <div class="font-black text-gray-900">
                                        {{ $fmt($daily['temperature_2m_max'][$i] ?? null) }}°
                                        <span class="font-semibold text-gray-400">/ {{ $fmt($daily['temperature_2m_min'][$i] ?? null) }}°</span>
                                    </div>
                                    @if($rainProb !== null)
                                        <div class="mt-3">
                                            <div class="flex items-center justify-center gap-1 text-xs font-bold {{ $rainy ? 'text-sky-700' : 'text-gray-500' }}">
                                                💧 {{ $rainProb }} %
                                                @if(($daily['precipitation_sum'][$i] ?? 0) > 0)
                                                    <span class="font-normal text-gray-400">· {{ $fmt($daily['precipitation_sum'][$i], 1) }} mm</span>
                                                @endif
                                            </div>
                                            <div class="mt-1 h-1.5 rounded-full bg-gray-200 overflow-hidden">
                                                <div class="h-full rounded-full {{ $rainy ? 'bg-sky-500' : 'bg-sky-300' }}" style="width: {{ $rainProb }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-3 px-1 text-xs text-gray-400">Dny s pravděpodobností deště 60 % a více jsou zvýrazněné modře. Data: Open-Meteo.</p>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                    <div class="text-gray-400 italic">
                        Nepodařilo se načíst data o počasí.
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
