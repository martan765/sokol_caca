<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\GameTypeService;

class UniqueGameTypeName implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(GameTypeService::class)->nameExists((string) $value, $this->ignoreId)) {
            $fail('validation.unique')->translate();
        }
    }
}
