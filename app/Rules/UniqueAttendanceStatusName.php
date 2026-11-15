<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\AttendanceService;

class UniqueAttendanceStatusName implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(AttendanceService::class)->statusNameExists((string) $value, $this->ignoreId)) {
            $fail('validation.unique')->translate();
        }
    }
}
