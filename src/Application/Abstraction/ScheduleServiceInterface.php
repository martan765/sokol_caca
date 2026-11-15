<?php

namespace Sokol\Application\Abstraction;

use DateTimeImmutable;
use Sokol\Domain\Enums\EventType;

interface ScheduleServiceInterface
{
    public function hasConflict(DateTimeImmutable $at, ?EventType $exceptType = null, ?int $exceptId = null): bool;

    public function minGapHours(): int;
}
