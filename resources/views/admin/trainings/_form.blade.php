{{-- Společná pole pro vytvoření i úpravu tréninku; $training je null při vytváření --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Datum a čas konání</label>
    <input type="datetime-local" name="training_date" value="{{ old('training_date', $training?->training_date->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('training_date')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Místo</label>
    <input type="text" name="location" value="{{ old('location', $training?->location ?? 'Hřiště Částkov') }}" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" required>
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Popis / Plán tréninku</label>
    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500" placeholder="Co se bude dneska dít?">{{ old('description', $training?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="flex items-center justify-end gap-4 mt-4">
    <a href="{{ route('admin.trainings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Zpět</a>
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-600 focus:bg-brand-600 active:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Uložit trénink
    </button>
</div>
