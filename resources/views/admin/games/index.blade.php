@use('App\View\Dates')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Správa zápasů') }}
            </h2>
            <a href="{{ route('admin.games.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 transition ease-in-out duration-150">
                + Nový zápas
            </a>
        </div>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Datum</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Zápas</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Typ</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Hřiště</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Výsledek</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($games as $game)
                            <tr class="{{ $game->isPast ? 'text-gray-400' : 'text-gray-700' }}">
                                <td class="px-4 py-3 whitespace-nowrap font-medium">
                                    {{ Dates::translated($game->date, 'D d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 font-semibold">{{ $game->homeTeam }} vs {{ $game->awayTeam }}</td>
                                <td class="px-4 py-3">{{ $game->typeName }}</td>
                                <td class="px-4 py-3">{{ $game->location }}</td>
                                <td class="px-4 py-3 font-bold">{{ $game->result ?? '–' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <x-attendance-link :href="route('admin.games.attendance', $game->id)" />
                                        <x-edit-link :href="route('admin.games.edit', $game->id)" />
                                        <x-delete-button :action="route('admin.games.destroy', $game->id)" confirm="Opravdu chceš SMAZAT tento zápas?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-400 italic">Žádné zápasy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="max-w-7xl mx-auto mt-4">
            {{ $games->links() }}
        </div>
    </div>
</x-app-layout>
