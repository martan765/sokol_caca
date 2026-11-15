<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Abstraction\GameTypeServiceInterface;

class ExistingGameType implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || ! app(GameTypeServiceInterface::class)->exists((int) $value)) {
            $fail('validation.exists')->translate();
        }
    }
}
