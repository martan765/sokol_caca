<?php

namespace App\Http\Requests\Admin;

use App\Rules\ExistingGameType;
use App\Rules\NoScheduleConflict;
use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Sokol\Application\Dto\GameData;
use Sokol\Domain\Enums\EventType;

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
        $id = $this->route('game');

        return [
            'game_date'     => ['bail', 'required', 'date', app(NoScheduleConflict::class)->ignoring($id === null ? null : (int) $id, EventType::Game)],
            'home_team'     => ['required', 'string', 'max:255'],
            'away_team'     => ['required', 'string', 'max:255'],
            'location'      => ['required', 'string', 'max:255'],
            'match_type_id' => ['required', 'integer', new ExistingGameType()],
            'result'        => ['nullable', 'string', 'max:20'],
        ];
    }

    public function toData(): GameData
    {
        return new GameData(
            (int) $this->validated('match_type_id'),
            $this->validated('home_team'),
            $this->validated('away_team'),
            new DateTimeImmutable($this->validated('game_date')),
            $this->validated('location'),
            $this->validated('result'),
        );
    }
}
