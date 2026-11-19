@php
    // Docházka jako semafor – stejné barvy pro tréninky i zápasy
    $tier     = fn (int $pct) => $pct >= 75 ? 'good' : ($pct >= 50 ? 'mid' : 'low');
    $pillCls  = ['good' => 'bg-brand-100 text-brand-800', 'mid' => 'bg-amber-100 text-amber-800', 'low' => 'bg-red-100 text-red-700'];
    $barCls   = ['good' => 'bg-brand-500', 'mid' => 'bg-amber-400', 'low' => 'bg-red-500'];
    $textCls  = ['good' => 'text-brand-700', 'mid' => 'text-amber-700', 'low' => 'text-red-600'];
    $dotCls   = ['good' => 'bg-brand-300', 'mid' => 'bg-amber-300', 'low' => 'bg-red-400'];
    $statKeys = ['trainings' => 'Tréninky', 'games' => 'Zápasy'];

    // Tabulky jsou proužkované (každý druhý řádek světle zelený), stav účasti ukazuje barevný levý okraj řádku
    $stripe       = fn (bool $even) => $even ? 'bg-brand-50' : 'bg-white';
    $statusBorder = fn ($statusId) => $statusId == 1 ? 'border-l-green-500' : ($statusId == 2 ? 'border-l-red-400' : 'border-l-transparent');
@endphp

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

            <!-- dashboard hráče -->
            @if(!$isAdmin)
                @if($grouped->isEmpty())
                    <div class="text-center py-16 text-gray-400 italic bg-white rounded-xl border border-gray-200">
                        Zatím žádné akce k zobrazení.
                    </div>
                @else
                    @foreach($grouped as $month => $events)
                        @php
                            $stats     = $monthlyStats[$month];
                            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('cs')->translatedFormat('F Y');
                        @endphp

                        <div x-data="{ open: true }" class="mb-5 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-6 py-4 bg-gradient-to-r from-brand-800 to-brand-600 hover:from-brand-700 hover:to-brand-500 transition-colors text-left">
                                <div class="flex items-center flex-wrap gap-3">
                                    <span class="text-base font-bold text-white capitalize">{{ $monthName }}</span>
                                    @foreach($statKeys as $key => $label)
                                        @if($stats[$key]['total'] > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold text-white bg-white/15 ring-1 ring-inset ring-white/25">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $dotCls[$tier($stats[$key]['percentage'])] }}"></span>
                                                <span>{{ $label }}</span>
                                                <span class="opacity-80">{{ $stats[$key]['attended'] }}/{{ $stats[$key]['total'] }}</span>
                                                <span class="opacity-60">·</span>
                                                <span>{{ $stats[$key]['percentage'] }}%</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <svg :class="open ? 'rotate-180' : ''"
                                     class="w-4 h-4 text-white/70 transition-transform duration-200 shrink-0 ml-3"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-brand-100 border-b border-brand-200">
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-brand-900/60 uppercase tracking-wider border-l-4 border-l-transparent">Datum</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-brand-900/60 uppercase tracking-wider">Typ</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-brand-900/60 uppercase tracking-wider">Místo / Soupeř</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-brand-900/60 uppercase tracking-wider">Účast</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-bold text-brand-900/60 uppercase tracking-wider">Poznámka</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($events->sortBy('date') as $event)
                                                <tr class="{{ $stripe($loop->even) }} hover:bg-brand-100/60 transition-colors">
                                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap border-l-4 {{ $statusBorder($event['status_id']) }}">
                                                        {{ $event['date']->locale('cs')->translatedFormat('D d.m.Y') }}
                                                        @if($event['date']->format('H:i') !== '00:00')
                                                            <span class="text-gray-500 text-xs ml-1">{{ $event['date']->format('H:i') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <x-event-badge :type="$event['type']" />
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

                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                                    @foreach($statKeys as $key => $label)
                                        @if($stats[$key]['total'] > 0)
                                            @php $t = $tier($stats[$key]['percentage']); @endphp
                                            <div>
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="text-gray-500">{{ $label }}</span>
                                                    <span class="font-bold text-gray-700">
                                                        {{ $stats[$key]['attended'] }}/{{ $stats[$key]['total'] }}
                                                        <span class="{{ $textCls[$t] }} ml-1">{{ $stats[$key]['percentage'] }}%</span>
                                                    </span>
                                                </div>
                                                <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $barCls[$t] }}" style="width: {{ $stats[$key]['percentage'] }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            <!-- dashboard admin -->
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
                            <h3 class="flex items-center gap-2 text-base font-bold text-gray-700 mb-3 px-1 uppercase tracking-wider">
                                <span class="w-1.5 h-5 rounded-full bg-brand-600"></span>
                                {{ $monthName }}
                                <span class="text-xs font-normal normal-case tracking-normal text-gray-400">({{ $monthEvents->count() }} {{ $monthEvents->count() < 5 ? 'akce' : 'akcí' }})</span>
                            </h3>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <table class="w-full text-sm" style="table-layout:fixed">
                                    <thead>
                                        <tr class="bg-brand-800">
                                            <th style="width:36%" class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Hráč</th>
                                            <th style="width:15%" class="px-4 py-3 text-center text-xs font-bold text-brand-100 uppercase tracking-wider">Tréninky</th>
                                            <th style="width:10%" class="px-4 py-3 text-center text-xs font-bold text-brand-100 uppercase tracking-wider">%</th>
                                            <th style="width:15%" class="px-4 py-3 text-center text-xs font-bold text-brand-100 uppercase tracking-wider">Zápasy</th>
                                            <th style="width:10%" class="px-4 py-3 text-center text-xs font-bold text-brand-100 uppercase tracking-wider">%</th>
                                            <th style="width:14%" class="px-4 py-3"></th>
                                        </tr>
                                    </thead>

                                    @foreach($players as $player)
                                        @php $stats = $playerMonthlyStats[$player->id][$month]; @endphp

                                        <tbody x-data="{ open: false }">
                                            <tr class="border-b border-gray-100 {{ $stripe($loop->even) }} hover:bg-brand-100/60 transition-colors">
                                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $player->name }}</td>
                                                @foreach(array_keys($statKeys) as $key)
                                                    <td class="px-4 py-3 text-center text-gray-600">
                                                        @if($stats[$key]['total'] > 0)
                                                            <span class="font-bold text-gray-800">{{ $stats[$key]['attended'] }}</span>
                                                            <span class="text-gray-400">/</span>
                                                            <span>{{ $stats[$key]['total'] }}</span>
                                                        @else
                                                            <span class="text-gray-300">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($stats[$key]['total'] > 0)
                                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $pillCls[$tier($stats[$key]['percentage'])] }}">
                                                                {{ $stats[$key]['percentage'] }}%
                                                            </span>
                                                        @else
                                                            <span class="text-gray-300">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="px-4 py-3 text-center">
                                                    <button @click="open = !open"
                                                            class="p-1.5 rounded-lg hover:bg-brand-100 transition-colors"
                                                            :title="open ? 'Skrýt detail' : 'Zobrazit detail'">
                                                        <svg :class="open ? 'rotate-180' : ''"
                                                             class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr x-show="open" x-transition>
                                                <td colspan="6" class="p-0 bg-gray-50 border-b border-gray-200">
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-xs">
                                                            <thead>
                                                                <tr class="bg-brand-100 border-b border-brand-200">
                                                                    <th class="pl-8 pr-4 py-2 text-left font-bold text-brand-900/60 uppercase tracking-wider border-l-4 border-l-transparent">Datum</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-brand-900/60 uppercase tracking-wider">Typ</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-brand-900/60 uppercase tracking-wider">Místo / Soupeř</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-brand-900/60 uppercase tracking-wider">Účast</th>
                                                                    <th class="px-4 py-2 text-left font-bold text-brand-900/60 uppercase tracking-wider">Poznámka</th>
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
                                                                    <tr class="{{ $stripe($loop->even) }}">
                                                                        <td class="pl-8 pr-4 py-2 text-gray-700 whitespace-nowrap font-medium border-l-4 {{ $statusBorder($statusId) }}">
                                                                            {{ $event['date']->locale('cs')->translatedFormat('D d.m.Y') }}
                                                                        </td>
                                                                        <td class="px-4 py-2">
                                                                            <x-event-badge :type="$event['type']" />
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
