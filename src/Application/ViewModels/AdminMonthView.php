<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class AdminMonthView
{
    public function __construct(
        public string $month,
        public DateTimeImmutable $monthStart,
        public int $eventCount,
        public array $players,
    ) {}
}
