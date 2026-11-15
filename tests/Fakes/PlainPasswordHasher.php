<?php

namespace Tests\Fakes;

use Sokol\Application\Ports\PasswordHasher;

final class PlainPasswordHasher implements PasswordHasher
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
