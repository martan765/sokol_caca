<?php

namespace Sokol\Application\Implementation;

use DateTimeImmutable;
use Sokol\Application\Abstraction\ScheduleServiceInterface;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Repositories\GameRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Schedule\ScheduleRules;

/**
 * Kalendář týmu – tréninky i zápasy dohromady.
 */
class ScheduleService implements ScheduleServiceInterface
{
    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
        private readonly GameRepositoryInterface $games,
    ) {}

    public function hasConflict(DateTimeImmutable $at, ?EventType $exceptType = null, ?int $exceptId = null): bool
    {
        [$from, $to] = ScheduleRules::blockedWindow($at);

        return $this->trainings->existsBetween($from, $to, $exceptType === EventType::Training ? $exceptId : null)
            || $this->games->existsBetween($from, $to, $exceptType === EventType::Game ? $exceptId : null);
    }

    public function minGapHours(): int
    {
        return ScheduleRules::minGapHours();
    }
}
