{{-- Společná pole pro vytvoření i úpravu zápasu; $game je null při vytváření --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Typ utkání</label>
    <select name="match_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
        <option value="">-- Vyber typ zápasu --</option>
        @foreach($gameTypes as $type)
            <option value="{{ $type->id }}" @selected(old('match_type_id', $game?->match_type_id) == $type->id)>{{ $type->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('match_type_id')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Datum a čas konání</label>
    <input type="datetime-local" name="game_date" value="{{ old('game_date', $game?->game_date->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('game_date')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Domácí</label>
    <input type="text" name="home_team" value="{{ old('home_team', $game?->home_team) }}" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('home_team')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Hosté</label>
    <input type="text" name="away_team" value="{{ old('away_team', $game?->away_team) }}" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('away_team')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Hřiště</label>
    <input type="text" name="location" value="{{ old('location', $game?->location) }}" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Výsledek (lze doplnit později)</label>
    <input type="text" name="result" value="{{ old('result', $game?->result) }}" maxlength="20" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" placeholder="např. 5:1">
    <x-input-error :messages="$errors->get('result')" class="mt-2" />
</div>

<div class="flex items-center justify-end gap-4 mt-4">
    <a href="{{ route('admin.games.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Zpět</a>
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 focus:bg-brand-600 active:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Uložit zápas
    </button>
</div>
