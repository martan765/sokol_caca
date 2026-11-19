<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class DashboardMonthView
{
    public function __construct(
        public string $month,
        public DateTimeImmutable $monthStart,
        public array $events,
        public array $stats,
    ) {}
}
