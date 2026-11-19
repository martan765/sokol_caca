<x-app-layout>
    <style>
        summary::-webkit-details-marker { display: none; }
        summary { list-style: none; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plán zápasů</h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($games as $game)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 border-t-4 border-t-gray-800 flex flex-col">

                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-start gap-4">
                            <x-date-tile :date="$game->game_date" />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-2xl font-black text-gray-900 leading-none">{{ $game->game_date->format('H:i') }}</span>
                                    <x-countdown :date="$game->game_date" />
                                    @if($game->isHome())
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-200">Doma</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-white text-gray-600 ring-1 ring-inset ring-gray-300">Venku</span>
                                    @endif
                                </div>

                                <div class="mt-2 text-base font-bold text-gray-800">
                                    @foreach([$game->home_team, $game->away_team] as $team)
                                        <span @class(['text-brand-700' => $game->isOurTeam($team)])>{{ $team }}</span>
                                        @if($loop->first) <span class="text-gray-400 font-normal mx-1">vs</span> @endif
                                    @endforeach
                                </div>

                                <div class="mt-1 flex items-center gap-1.5 text-sm text-gray-500">
                                    @if($game->location)
                                        <svg class="w-4 h-4 shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="truncate">{{ $game->location }}</span>
                                    @endif
                                    @if($game->type)
                                        <span class="text-gray-300">·</span>
                                        <span class="truncate">{{ $game->type->name }}</span>
                                    @endif
                                </div>
                            </div>

                            @if(auth()->user()->isAdmin())
                                <x-edit-link :href="route('admin.games.edit', $game)" />
                            @endif
                        </div>
                    </div>

                    <x-attendance-vote :users="$game->users" field="game_id" :event-id="$game->id" :statuses="$statuses" />
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-gray-400 italic bg-white rounded-xl border">Žádné zápasy.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
