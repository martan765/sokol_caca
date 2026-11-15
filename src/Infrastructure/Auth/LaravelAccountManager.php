<?php

namespace Sokol\Infrastructure\Auth;

use Illuminate\Support\Facades\Auth;
use Sokol\Domain\Interfaces\Managers\AccountManagerInterface;

final class LaravelAccountManager implements AccountManagerInterface
{
    public function login(string $email, string $password, bool $remember): bool
    {
        return Auth::guard('web')->attempt(['email' => $email, 'password' => $password], $remember);
    }

    public function loginById(int $userId): void
    {
        Auth::guard('web')->loginUsingId($userId);
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
