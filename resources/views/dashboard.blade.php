<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($isAdmin)
                Docházka hráčů
            @else
                Přehled mé docházky
            @endif
        </h2>
    </x-slot>

    <div class="py-8 px-4">
        <div class="max-w-7xl mx-auto">

            {{-- ========================================
                 HRÁČ: není propojen s profilem
            ========================================= --}}
            @if(!$isAdmin && ($noPlayer ?? false))
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-yellow-800 font-medium">
                    Tvůj účet není propojen s profilem hráče. Kontaktuj trenéra.
                </div>

            {{-- ========================================
                 DASHBOARD HRÁČE
            ========================================= --}}
            @elseif(!$isAdmin)
                @if($grouped->isEmpty())
                    <div class="text-center py-16 text-gray-400 italic bg-white rounded-xl border border-gray-200">
                        Zatím žádné akce k zobrazení.
                    </div>
                @else
                    @foreach($grouped as $month => $events)
                        @php
                            $stats     = $monthlyStats[$month];
                            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('cs')->translatedFormat('F Y');
                            $tPct      = $stats['trainings']['percentage'];
                            $gPct      = $stats['games']['percentage'];
                            $tBg       = $tPct >= 75 ? 'bg-blue-500' : ($tPct >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                            $gBg       = $gPct >= 75 ? 'bg-purple-500' : ($gPct >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                        @endphp

                        <div x-data="{ open: true }" class="mb-5 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                            {{-- Měsíční hlavička --}}
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors text-left">
                                <div class="flex items-center flex-wrap gap-3">
                                    <span class="text-base font-bold text-gray-800 capitalize">{{ $monthName }}</span>
                                    @if($stats['trainings']['total'] > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold text-white {{ $tBg }}">
                                            <span>Tréninky</span>
                                            <span class="opacity-90">{{ $stats['trainings']['attended'] }}/{{ $stats['trainings']['total'] }}</span>
                                            <span class="opacity-75">·</span>
                                            <span>{{ $tPct }}%</span>
                                        </span>
                                    @endif
                                    @if($stats['games']['total'] > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold text-white {{ $gBg }}">
                                            <span>Zápasy</span>
                                            <span class="opacity-90">{{ $stats['games']['attended'] }}/{{ $stats['games']['total'] }}</span>
                                            <span class="opacity-75">·</span>
                                            <span>{{ $gPct }}%</span>
                                        </span>
                                    @endif
                                </div>
                                <svg :class="open ? 'rotate-180' : ''"
                                     class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-3"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Detail tabulka --}}
                            <div x-show="open" x-transition>
                                <div class="overflow-x-auto border-t border-gray-100">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Datum</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Typ</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Místo / Soupeř</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Účast</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Poznámka</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($events->sortBy('date') as $event)
                                                <tr class="{{ $event['status_id'] == 1 ? 'bg-green-50' : ($event['status_id'] == 2 ? 'bg-red-50' : '') }} hover:opacity-90 transition-opacity">
                                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                                        {{ $event['date']->locale('cs')->translatedFormat('D d.m.Y') }}
                                                        @if($event['date']->format('H:i') !== '00:00')
                                                            <span class="text-gray-500 text-xs ml-1">{{ $event['date']->format('H:i') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        @if($event['type'] === 'training')
                                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Trénink</span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Zápas</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-700">
                                                        @if($event['type'] === 'game')
                                                            <span class="font-medium">{{ $event['label'] }}</span>
                                                            @if($event['location'])
                                                                <span class="text-gray-400 text-xs ml-1">({{ $event['location'] }})</span>
                                                            @endif
                                                        @else
                                                            {{ $event['location'] ?? '—' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        @if($event['status_id'] == 1)
                                                            <span class="inline-flex items-center gap-1 font-semibold text-green-700">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Byl
                                                            </span>
                                                        @elseif($event['status_id'] == 2)
                                                            <span class="inline-flex items-center gap-1 font-semibold text-red-600">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Nebyl
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 text-xs">— nevyplněno</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500 text-sm italic">
                                                        {{ ($event['note'] ?? '') ?: '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Spodní souhrn --}}
                                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center gap-6 text-sm">
                                    @if($stats['trainings']['total'] > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span class="text-gray-500">Tréninky:</span>
                                            <span class="font-bold text-gray-700">{{ $stats['trainings']['attended'] }}/{{ $stats['trainings']['total'] }}</span>
                                            <span class="font-bold text-blue-600">{{ $tPct }}%</span>
                                        </div>
                                    @endif
                                    @if($stats['games']['total'] > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            <span class="text-gray-500">Zápasy:</span>
                                            <span class="font-bold text-gray-700">{{ $stats['games']['attended'] }}/{{ $stats['games']['total'] }}</span>
                                            <span class="font-bold text-purple-600">{{ $gPct }}%</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            {{-- ========================================
                 DASHBOARD TRENÉRA (ADMIN)
            ========================================= --}}
            @else
                @if($months->isEmpty())
                    <div class="text-center py-16 text-gray-400 italic bg-white rounded-xl border border-gray-200">
                        Zatím žádné akce k zobrazení.
                    </div>
                @else
                    @foreach($months as $month)
                        @php
                            $monthName   = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('cs')->translatedFormat('F Y');
                            $monthEvents = $eventsByMonth[$month];
                        @endphp

                        <div class="mb-8">
                            <h3 class="text-base font-bold text-gray-600 capitalize mb-3 px-1 uppercase tracking-wider">
                                {{ $monthName }}
                                <span class="text-xs font-normal text-gray-400 ml-2">({{ $monthEvents->count() }} {{ $monthEvents->count() === 1 ? 'akce' : ($monthEvents->count() < 5 ? 'akce' : 'akcí') }})</span>
                            </h3>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <table class="w-full text-sm" style="table-layout:fixed">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th style="width:36%" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hráč</th>
                                            <th style="width:15%" class="px-4 py-3 text-center text-xs font-bold text-blue-500 uppercase tracking-wider">Tréninky</th>
                                            <th style="width:10%" class="px-4 py-3 text-center text-xs font-bold text-blue-500 uppercase tracking-wider">%</th>
                                            <th style="width:15%" class="px-4 py-3 text-center text-xs font-bold text-purple-500 uppercase tracking-wider">Zápasy</th>
                                            <th style="width:10%" class="px-4 py-3 text-center text-xs font-bold text-purple-500 uppercase tracking-wider">%</th>
                                            <th style="width:14%" class="px-4 py-3"></th>
                                        </tr>
                                    </thead>

                                    @foreach($players as $player)
                                        @php
                                            $stats      = $playerMonthlyStats[$player->id][$month];
                                            $tPct       = $stats['trainings']['percentage'];
                                            $gPct       = $stats['games']['percentage'];
                                            $tClass     = $tPct >= 75 ? 'text-blue-700 bg-blue-100' : ($tPct >= 50 ? 'text-yellow-700 bg-yellow-100' : 'text-red-700 bg-red-100');
                                            $gClass     = $gPct >= 75 ? 'text-purple-700 bg-purple-100' : ($gPct >= 50 ? 'text-yellow-700 bg-yellow-100' : 'text-red-700 bg-red-100');
                                            $playerName = $player->name;
                                        @endphp

                                        <tbody x-data="{ open: false }">
                                            {{-- Souhrnný řádek hráče --}}
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $playerName }}</td>
                                                <td class="px-4 py-3 text-center text-gray-600">
                                                    @if($stats['trainings']['total'] > 0)
                                                        <span class="font-bold text-gray-800">{{ $stats['trainings']['attended'] }}</span>
                                                        <span class="text-gray-400">/</span>
                                                        <span>{{ $stats['trainings']['total'] }}</span>
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($stats['trainings']['total'] > 0)
                                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tClass }}">
                                                            {{ $tPct }}%
                                                        </span>
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-600">
                                                    @if($stats['games']['total'] > 0)
                                                        <span class="font-bold text-gray-800">{{ $stats['games']['attended'] }}</span>
                                                        <span class="text-gray-400">/</span>
                                                        <span>{{ $stats['games']['total'] }}</span>
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($stats['games']['total'] > 0)
                                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $gClass }}">
                                                            {{ $gPct }}%
                                                        </span>
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button @click="open = !open"
                                                            class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors"
                                                            :title="open ? 'Skrýt detail' : 'Zobrazit detail'">
                                                        <svg :class="open ? 'rotate-180' : ''"
                                                             class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Detail řádek (rozbalitelný) --}}
                                            <tr x-show="open" x-transition>
                                                <td colspan="6" class="p-0 bg-gray-50 border-b border-gray-200">
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-xs">
                                                            <thead>
                                                                <tr class="bg-gray-100 border-b border-gray-200">
                                                                    <th class="pl-8 pr-4 py-2 text-left font-bold text-gray-500 uppercase tracking-wider">Datum</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-gray-500 uppercase tracking-wider">Typ</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-gray-500 uppercase tracking-wider">Místo / Soupeř</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-gray-500 uppercase tracking-wider">Účast</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-gray-500 uppercase tracking-wider">Poznámka</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach($monthEvents->sortBy('date') as $event)
                                                                    @php
                                                                        $att      = $event['type'] === 'training'
                                                                            ? $trainAtt->get($player->id)?->get($event['id'])
                                                                            : $gameAtt->get($player->id)?->get($event['id']);
                                                                        $statusId = $att?->status_id;
                                                                        $note     = $att?->note;
                                                                    @endphp
                                                                    <tr class="{{ $statusId == 1 ? 'bg-green-50' : ($statusId == 2 ? 'bg-red-50' : '') }}">
                                                                        <td class="pl-8 pr-4 py-2 text-gray-700 whitespace-nowrap font-medium">
                                                                            {{ $event['date']->locale('cs')->translatedFormat('D d.m.Y') }}
                                                                        </td>
                                                                        <td class="px-4 py-2">
                                                                            @if($event['type'] === 'training')
                                                                                <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 font-bold">Trénink</span>
                                                                            @else
                                                                                <span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 font-bold">Zápas</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-4 py-2 text-gray-600">
                                                                            @if($event['type'] === 'game')
                                                                                {{ $event['label'] }}
                                                                                @if($event['location'])
                                                                                    <span class="text-gray-400">({{ $event['location'] }})</span>
                                                                                @endif
                                                                            @else
                                                                                {{ $event['location'] ?? '—' }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-4 py-2 whitespace-nowrap">
                                                                            @if($statusId == 1)
                                                                                <span class="inline-flex items-center gap-1 text-green-700 font-semibold">
                                                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                                    </svg>
                                                                                    Byl
                                                                                </span>
                                                                            @elseif($statusId == 2)
                                                                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                                    </svg>
                                                                                    Nebyl
                                                                                </span>
                                                                            @else
                                                                                <span class="text-gray-400">— nevyplněno</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-4 py-2 text-gray-500 italic">
                                                                            {{ ($note ?? '') ?: '—' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
