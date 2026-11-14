<?php

namespace Sokol\Infrastructure\System;

use DateTimeImmutable;
use Sokol\Application\Ports\Clock;

final class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface(now());
    }
}
