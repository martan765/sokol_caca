<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Uživatelé') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Jméno</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    {{ $user->name }}
                                    @if($user->is(auth()->user()))
                                        <span class="text-xs text-gray-400">(ty)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @if($user->isAdmin())
                                        <span class="px-2 py-0.5 rounded bg-brand-100 text-brand-700 text-xs font-bold">Trenér</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-xs font-bold">Hráč</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <x-edit-link :href="route('admin.users.edit', $user)" />
                                        @can('delete', $user)
                                            <x-delete-button :action="route('admin.users.destroy', $user)" confirm="Opravdu chceš SMAZAT tohoto uživatele? Smaže se i jeho docházka." />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="max-w-7xl mx-auto mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
