<?php

namespace App\Http\Requests;

use App\Rules\ExistingAttendanceStatus;
use App\Rules\ExistingEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Sokol\Application\Dto\AttendanceVoteData;
use Sokol\Domain\Enums\EventType;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'      => ['required', 'integer', new ExistingAttendanceStatus()],
            'training_id' => ['nullable', 'required_without:game_id', 'prohibits:game_id', 'integer', new ExistingEvent(EventType::Training)],
            'game_id'     => ['nullable', 'required_without:training_id', 'integer', new ExistingEvent(EventType::Game)],
        ];
    }

    public function isTrainingVote(): bool
    {
        return $this->filled('training_id');
    }

    public function toData(): AttendanceVoteData
    {
        $type = $this->isTrainingVote() ? EventType::Training : EventType::Game;

        return new AttendanceVoteData(
            (int) Auth::id(),
            $type,
            (int) $this->validated($type === EventType::Training ? 'training_id' : 'game_id'),
            (int) $this->validated('status'),
        );
    }
}
