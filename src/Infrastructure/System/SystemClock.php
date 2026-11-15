<?php

namespace Sokol\Infrastructure\System;

use DateTimeImmutable;
use Sokol\Domain\Interfaces\Managers\ClockInterface;

final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface(now());
    }
}
