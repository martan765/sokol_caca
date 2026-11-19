<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Abstraction\UserServiceInterface;

class UniqueEmail implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreUserId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(UserServiceInterface::class)->isEmailTaken((string) $value, $this->ignoreUserId)) {
            $fail('validation.unique')->translate();
        }
    }
}
