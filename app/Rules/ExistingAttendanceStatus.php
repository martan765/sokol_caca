<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\AttendanceService;

class ExistingAttendanceStatus implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || ! app(AttendanceService::class)->statusExists((int) $value)) {
            $fail('validation.exists')->translate();
        }
    }
}
