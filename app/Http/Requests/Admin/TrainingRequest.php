<?php

namespace App\Http\Requests\Admin;

use App\Rules\NoScheduleConflict;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validace pro vytvoření i úpravu tréninku.
 */
class TrainingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Při úpravě je v routě upravovaný trénink, při vytvoření null
            'training_date' => ['bail', 'required', 'date', app(NoScheduleConflict::class)->ignoring($this->route('training'))],
            'location'      => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
        ];
    }
}
