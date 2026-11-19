<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Správa tréninků') }}
            </h2>
            <a href="{{ route('admin.trainings.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 transition ease-in-out duration-150">
                + Nový trénink
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
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Místo</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Popis</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($trainings as $training)
                            <tr class="{{ $training->training_date->isPast() ? 'text-gray-400' : 'text-gray-700' }}">
                                <td class="px-4 py-3 whitespace-nowrap font-medium">
                                    {{ $training->training_date->locale('cs')->translatedFormat('D d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3">{{ $training->location }}</td>
                                <td class="px-4 py-3 max-w-xs truncate">{{ $training->description }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <x-attendance-link :href="route('admin.trainings.attendance', $training)" />
                                        <x-edit-link :href="route('admin.trainings.edit', $training)" />
                                        <x-delete-button :action="route('admin.trainings.destroy', $training)" confirm="Opravdu chceš SMAZAT tento trénink?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-gray-400 italic">Žádné tréninky.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="max-w-7xl mx-auto mt-4">
            {{ $trainings->links() }}
        </div>
    </div>
</x-app-layout>
