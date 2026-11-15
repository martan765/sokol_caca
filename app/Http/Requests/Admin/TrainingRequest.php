<?php

namespace App\Http\Requests\Admin;

use App\Rules\NoScheduleConflict;
use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Sokol\Application\Dto\TrainingData;
use Sokol\Domain\Enums\EventType;

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
        $id = $this->route('training');

        return [
            'training_date' => ['bail', 'required', 'date', app(NoScheduleConflict::class)->ignoring($id === null ? null : (int) $id, EventType::Training)],
            'location'      => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
        ];
    }

    public function toData(): TrainingData
    {
        return new TrainingData(
            new DateTimeImmutable($this->validated('training_date')),
            $this->validated('location'),
            $this->validated('description'),
        );
    }
}
