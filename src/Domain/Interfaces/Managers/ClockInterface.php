<?php

namespace Sokol\Domain\Interfaces\Managers;

use DateTimeImmutable;

interface ClockInterface
{
    public function now(): DateTimeImmutable;
}
