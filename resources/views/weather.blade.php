<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Aktuální počasí
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            @if(!empty($weather['current']))
                @php
                    $current = $weather['current'];
                    $weatherCode = $current['weather_code'] ?? null;
                    $weatherDescription = match (true) {
                        $weatherCode === 0 => 'Jasno',
                        in_array($weatherCode, [1, 2], true) => 'Polojasno',
                        $weatherCode === 3 => 'Zataženo',
                        in_array($weatherCode, [45, 48], true) => 'Mlha',
                        in_array($weatherCode, [51, 53, 55, 56, 57], true) => 'Mrholení',
                        in_array($weatherCode, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'Déšť',
                        in_array($weatherCode, [71, 73, 75, 77, 85, 86], true) => 'Sněžení',
                        in_array($weatherCode, [95, 96, 99], true) => 'Bouřka',
                        default => 'Neznámé podmínky',
                    };
                @endphp

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-2.636-1.591 1.591M5.25 12H3m3.636-5.364L5.045 5.045M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Částkov
                                </div>
                                <div class="text-xl font-black text-gray-900">
                                    Aktuální podmínky
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-lg bg-blue-50 border border-blue-100 p-4">
                                <div class="text-xs font-bold text-blue-500 uppercase tracking-wider">Teplota</div>
                                <div class="mt-2 text-3xl font-black text-gray-900">
                                    {{ $current['temperature_2m'] ?? '—' }} <span class="text-lg font-bold text-gray-500">°C</span>
                                </div>
                            </div>

                            <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rychlost větru</div>
                                <div class="mt-2 text-3xl font-black text-gray-900">
                                    {{ $current['wind_speed_10m'] ?? '—' }} <span class="text-lg font-bold text-gray-500">km/h</span>
                                </div>
                            </div>

                            <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stav počasí</div>
                                <div class="mt-2 text-3xl font-black text-gray-900">
                                    {{ $weatherDescription }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
