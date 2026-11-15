<?php

namespace Tests\Fakes;

use Sokol\Domain\Interfaces\Managers\PasswordHasherInterface;

final class PlainPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return 'hashed:' . $plainPassword;
    }

    public function check(string $plainPassword, string $hash): bool
    {
        return $hash === 'hashed:' . $plainPassword;
    }
}
