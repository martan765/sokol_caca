<?php

namespace Sokol\Infrastructure\Auth;

use Illuminate\Contracts\Hashing\Hasher;
use Sokol\Application\Ports\PasswordHasher;

final class LaravelPasswordHasher implements PasswordHasher
{
    public function __construct(
        private readonly Hasher $hasher,
    ) {}

    public function hash(string $plainPassword): string
    {
        return $this->hasher->make($plainPassword);
    }

    public function check(string $plainPassword, string $hash): bool
    {
        return $hash !== '' && $this->hasher->check($plainPassword, $hash);
    }
}
