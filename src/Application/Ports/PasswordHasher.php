<?php

namespace Sokol\Application\Ports;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function check(string $plainPassword, string $hash): bool;
}
