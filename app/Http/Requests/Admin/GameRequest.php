<?php

namespace App\Http\Requests\Admin;

use App\Rules\NoScheduleConflict;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validace pro vytvoření i úpravu zápasu.
 */
class GameRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Při úpravě je v routě upravovaný zápas, při vytvoření null
            'game_date'     => ['bail', 'required', 'date', app(NoScheduleConflict::class)->ignoring($this->route('game'))],
            'home_team'     => ['required', 'string', 'max:255'],
            'away_team'     => ['required', 'string', 'max:255'],
            'location'      => ['required', 'string', 'max:255'],
            'match_type_id' => ['required', 'integer', 'exists:game_types,id'],
            'result'        => ['nullable', 'string', 'max:20'],
        ];
    }
}
