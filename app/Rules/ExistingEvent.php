<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\GameService;
use Sokol\Application\Services\TrainingService;
use Sokol\Domain\Enums\EventType;

class ExistingEvent implements ValidationRule
{
    public function __construct(
        private readonly EventType $type,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = is_numeric($value) && match ($this->type) {
            EventType::Training => app(TrainingService::class)->exists((int) $value),
            EventType::Game     => app(GameService::class)->exists((int) $value),
        };

        if (! $exists) {
            $fail('validation.exists')->translate();
        }
    }
}
