<x-app-layout>
    <style>
        summary::-webkit-details-marker { display: none; }
        summary { list-style: none; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plán tréninků') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($trainings as $training)
                @php
    $currentUser    = auth()->user();
    $userAttendance = $training->users->where('id', $currentUser->id)->first();
    $currentStatus  = $userAttendance ? $userAttendance->pivot->status_id : null;
@endphp

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 flex flex-col relative">

                    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-xs font-bold text-black-600 uppercase tracking-wider">
                                    {{ \Carbon\Carbon::parse($training->training_date)->locale('cs')->translatedFormat('l d.m.Y') }}
                                </div>
                                <div class="text-xl font-black text-gray-900">
                                    {{ \Carbon\Carbon::parse($training->training_date)->format('H:i') }}
                                </div>
                            </div>

                            <div class="flex flex-col items-end text-right pl-4">
                                @if(auth()->user() && auth()->user()->role === 'admin')
                                    <div class="mb-1">
                                        <form action="{{ route('trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('Opravdu chceš SMAZAT tento trénink?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-white text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-full border border-gray-100 hover:border-red-100 transition-all duration-200 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                                <span class="text-lg font-bold text-gray-800 leading-tight">{{ $training->location }}</span>
                            </div>
                        </div>

                        @if($training->description)
                            <p class="text-sm text-gray-500 mt-2 italic">{{ $training->description }}</p>
                        @endif
                    </div>

                    <div class="p-5">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Tvoje účast</div>

                        <div class="mb-6">
                            <form action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="training_id" value="{{ $training->id }}">
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="w-full flex items-center justify-between p-3 rounded-lg transition {{ $currentStatus == 1 ? 'bg-green-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center {{ $currentStatus == 1 ? 'border-green-500 bg-green-500' : 'border-gray-300' }}">
                                            @if($currentStatus == 1) <div class="w-2 h-2 bg-white rounded-full"></div> @endif
                                        </div>
                                        <span class="ml-3 font-semibold {{ $currentStatus == 1 ? 'text-green-600' : 'text-gray-700' }}">Budu</span>
                                    </div>
                                    <span class="font-bold text-green-600">{{ $training->users->where('pivot.status_id', 1)->count() }}</span>
                                </button>
                            </form>

                            @if($training->users->where('pivot.status_id', 1)->count() > 0)
                                <details class="px-3 mt-2">
                                    <summary class="text-[10px] font-bold text-gray-400 cursor-pointer hover:text-green-500 transition">Zobrazit jména</summary>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($training->users->where('pivot.status_id', 1) as $p)
                                            <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full font-bold border border-green-200">
                                                {{ $p->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </div>

                        <div>
                            <form action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="training_id" value="{{ $training->id }}">
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="w-full flex items-center justify-between p-3 rounded-lg transition {{ $currentStatus == 2 ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center {{ $currentStatus == 2 ? 'border-red-500 bg-red-500' : 'border-gray-300' }}">
                                            @if($currentStatus == 2) <div class="w-2 h-2 bg-white rounded-full"></div> @endif
                                        </div>
                                        <span class="ml-3 font-semibold {{ $currentStatus == 2 ? 'text-red-600' : 'text-gray-700' }}">Nebudu</span>
                                    </div>
                                    <span class="font-bold text-red-600">{{ $training->users->where('pivot.status_id', 2)->count() }}</span>
                                </button>
                            </form>

                            @if($training->users->where('pivot.status_id', 2)->count() > 0)
                                <details class="px-3 mt-2">
                                    <summary class="text-[10px] font-bold text-gray-400 cursor-pointer hover:text-green-500 transition">Zobrazit jména</summary>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($training->users->where('pivot.status_id', 2) as $p)
                                            <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full font-bold border border-green-200">
                                                {{ $p->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-gray-400 italic bg-white rounded-xl border">Žádné zápasy.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
