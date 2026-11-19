@props(['voters', 'myStatusId' => null, 'field', 'eventId', 'statuses' => []])

@use('Sokol\Domain\Enums\AttendanceStatusId')

@php
    // Názvy stavů lze přejmenovat v administraci, barvy jsou svázané s pevnými ID
    $labels    = array_column($statuses, 'name', 'id');
    $attending = AttendanceStatusId::Attending->value;
    $absent    = AttendanceStatusId::NotAttending->value;
    $options = [
        $attending => ['label' => $labels[$attending] ?? 'Budu',   'voters' => $voters[$attending] ?? [], 'text' => 'text-green-600', 'bg' => 'bg-green-50', 'dot' => 'border-green-500 bg-green-500', 'chip' => 'bg-green-100 text-green-700 border-green-200'],
        $absent    => ['label' => $labels[$absent] ?? 'Nebudu',    'voters' => $voters[$absent] ?? [],    'text' => 'text-red-600',   'bg' => 'bg-red-50',   'dot' => 'border-red-500 bg-red-500',     'chip' => 'bg-red-100 text-red-700 border-red-200'],
    ];
@endphp

<div class="p-5">
    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Tvoje účast</div>

    @foreach($options as $status => $opt)
        @php $selected = $myStatusId == $status; @endphp

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
                    <span class="font-bold {{ $opt['text'] }}">{{ count($opt['voters']) }}</span>
                </button>
            </form>

            @if(! empty($opt['voters']))
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
