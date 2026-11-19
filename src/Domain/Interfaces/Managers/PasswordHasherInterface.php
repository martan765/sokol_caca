<?php

namespace Sokol\Domain\Interfaces\Managers;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;

    public function check(string $plainPassword, string $hash): bool;
}
