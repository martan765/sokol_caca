<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stavy účasti') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <p class="text-sm text-gray-500 mb-4">
                Stavy jsou pevnou součástí systému (statistiky docházky s nimi počítají), proto je lze jen přejmenovat.
                Nový název se zobrazí na tlačítkách pro hlasování.
            </p>

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Název</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Význam</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($statuses as $status)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $status->name }}</td>
                                    <td class="px-4 py-3">
                                        @if($status->id == \App\Models\AttendanceStatus::ATTENDING)
                                            <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 text-xs font-bold">Hráč přijde</span>
                                        @elseif($status->id == \App\Models\AttendanceStatus::NOT_ATTENDING)
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs font-bold">Hráč nepřijde</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end">
                                            <x-edit-link :href="route('admin.attendance-statuses.edit', $status)" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
