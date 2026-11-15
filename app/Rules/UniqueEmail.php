<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\UserService;

class UniqueEmail implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreUserId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(UserService::class)->isEmailTaken((string) $value, $this->ignoreUserId)) {
            $fail('validation.unique')->translate();
        }
    }
}
