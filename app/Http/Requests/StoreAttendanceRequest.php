<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'status'      => ['required', 'integer', 'exists:attendance_statuses,id'],
            'training_id' => ['nullable', 'required_without:game_id', 'prohibits:game_id', 'integer', 'exists:trainings,id'],
            'game_id'     => ['nullable', 'required_without:training_id', 'integer', 'exists:games,id'],
        ];
    }

    public function isTrainingVote(): bool
    {
        return $this->filled('training_id');
    }
}
