<?php

namespace Tests\Fakes;

use DateTimeImmutable;
use Sokol\Domain\Interfaces\Managers\ClockInterface;

final class FixedClock implements ClockInterface
{
    public DateTimeImmutable $now;

    public function __construct(?DateTimeImmutable $now = null)
    {
        $this->now = $now ?? new DateTimeImmutable('2026-11-18 12:00:00');
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
