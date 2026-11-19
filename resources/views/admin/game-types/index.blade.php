<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Typy zápasů') }}
            </h2>
            <a href="{{ route('admin.game-types.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 transition ease-in-out duration-150">
                + Nový typ
            </a>
        </div>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Název</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Počet zápasů</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($types as $type)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                                <td class="px-4 py-3">{{ $type->games_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <x-edit-link :href="route('admin.game-types.edit', $type)" />
                                        @if($type->games_count == 0)
                                            <x-delete-button :action="route('admin.game-types.destroy', $type)" confirm="Opravdu chceš SMAZAT tento typ zápasu?" />
                                        @else
                                            <span class="w-[30px]" title="Typ používají zápasy, nelze ho smazat"></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400 italic">Žádné typy zápasů.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
