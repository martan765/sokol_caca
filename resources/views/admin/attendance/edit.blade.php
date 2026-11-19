<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $heading }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $date->locale('cs')->translatedFormat('l d.m.Y H:i') }}</p>
    </x-slot>

    <div class="py-12 px-4">
        <form action="{{ $action }}" method="POST" class="max-w-4xl mx-auto">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Hráč</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Účast</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Poznámka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($players as $player)
                                @php
                                    $att      = $current->get($player->id);
                                    $selected = old("attendance.{$player->id}.status_id", $att?->status_id);
                                    $field    = "attendance[{$player->id}]";
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $player->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-4 whitespace-nowrap">
                                            <label class="inline-flex items-center gap-1.5 text-gray-400">
                                                <input type="radio" name="{{ $field }}[status_id]" value="" @checked(! $selected) class="text-gray-400 focus:ring-gray-400">
                                                Nevyplněno
                                            </label>
                                            @foreach($statuses as $status)
                                                <label @class(['inline-flex items-center gap-1.5 font-semibold', 'text-green-700' => $status->id == \App\Models\AttendanceStatus::ATTENDING, 'text-red-600' => $status->id == \App\Models\AttendanceStatus::NOT_ATTENDING])>
                                                    <input type="radio" name="{{ $field }}[status_id]" value="{{ $status->id }}" @checked($selected == $status->id) class="text-brand-600 focus:ring-brand-500">
                                                    {{ $status->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 min-w-[12rem]">
                                        <input type="text" name="{{ $field }}[note]" value="{{ old("attendance.{$player->id}.note", $att?->note) }}" maxlength="255" placeholder="např. omluven telefonicky" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-brand-500 focus:border-brand-500">
                                        <x-input-error :messages="array_merge($errors->get('attendance.' . $player->id . '.status_id'), $errors->get('attendance.' . $player->id . '.note'))" class="mt-1" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-10 text-gray-400 italic">Žádní hráči.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-4">
                <a href="{{ $back }}" class="text-sm text-gray-600 hover:text-gray-900">Zpět</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Uložit docházku
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
