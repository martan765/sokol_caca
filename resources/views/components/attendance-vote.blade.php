@props(['users', 'field', 'eventId', 'statuses' => collect()])

@php
    // Názvy stavů lze přejmenovat v administraci, barvy jsou svázané s pevnými ID
    $labels = $statuses->pluck('name', 'id');
    $currentStatus = $users->firstWhere('id', auth()->id())?->pivot->status_id;
    $options = [
        1 => ['label' => $labels[1] ?? 'Budu',   'voters' => $users->where('pivot.status_id', 1), 'text' => 'text-green-600', 'bg' => 'bg-green-50', 'dot' => 'border-green-500 bg-green-500', 'chip' => 'bg-green-100 text-green-700 border-green-200'],
        2 => ['label' => $labels[2] ?? 'Nebudu', 'voters' => $users->where('pivot.status_id', 2), 'text' => 'text-red-600',   'bg' => 'bg-red-50',   'dot' => 'border-red-500 bg-red-500',     'chip' => 'bg-red-100 text-red-700 border-red-200'],
    ];
@endphp

<div class="p-5">
    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Tvoje účast</div>

    @foreach($options as $status => $opt)
        @php $selected = $currentStatus == $status; @endphp

        <div class="{{ $loop->last ? '' : 'mb-6' }}">
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="{{ $field }}" value="{{ $eventId }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <button type="submit" class="w-full flex items-center justify-between p-3 rounded-lg transition {{ $selected ? $opt['bg'] : 'hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center {{ $selected ? $opt['dot'] : 'border-gray-300' }}">
                            @if($selected) <div class="w-2 h-2 bg-white rounded-full"></div> @endif
                        </div>
                        <span class="ml-3 font-semibold {{ $selected ? $opt['text'] : 'text-gray-700' }}">{{ $opt['label'] }}</span>
                    </div>
                    <span class="font-bold {{ $opt['text'] }}">{{ $opt['voters']->count() }}</span>
                </button>
            </form>

            @if($opt['voters']->isNotEmpty())
                <details class="px-3 mt-2">
                    <summary class="text-[10px] font-bold text-gray-400 cursor-pointer hover:text-gray-600 transition">Zobrazit jména</summary>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($opt['voters'] as $p)
                            <span class="{{ $opt['chip'] }} text-[10px] px-2 py-0.5 rounded-full font-bold border">
                                {{ $p->name }}
                            </span>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
    @endforeach
</div>
