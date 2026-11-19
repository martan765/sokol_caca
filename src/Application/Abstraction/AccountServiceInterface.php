<?php

namespace Sokol\Application\Abstraction;

interface AccountServiceInterface
{
    public function login(string $email, string $password, bool $remember): bool;

    public function loginById(int $userId): void;

    public function logout(): void;
}
