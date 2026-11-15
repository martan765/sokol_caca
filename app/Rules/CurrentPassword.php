<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Sokol\Application\Services\UserService;

class CurrentPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = Auth::id();

        if ($id === null || ! app(UserService::class)->checkPassword((int) $id, (string) $value)) {
            $fail('validation.current_password')->translate();
        }
    }
}
