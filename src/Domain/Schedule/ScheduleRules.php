<?php

namespace Sokol\Domain\Schedule;

use DateInterval;
use DateTimeImmutable;

final class ScheduleRules
{
    public const MIN_GAP_MINUTES = 120;

    public static function blockedWindow(DateTimeImmutable $at): array
    {
        $gap = new DateInterval('PT' . self::MIN_GAP_MINUTES . 'M');

        return [$at->sub($gap), $at->add($gap)];
    }

    public static function minGapHours(): int
    {
        return intdiv(self::MIN_GAP_MINUTES, 60);
    }
}
