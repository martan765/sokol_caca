<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nový typ zápasu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.game-types.store') }}" method="POST">
                    @csrf
                    @include('admin.game-types._form', ['type' => null])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
