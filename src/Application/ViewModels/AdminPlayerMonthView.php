<?php

namespace Sokol\Application\ViewModels;

final readonly class AdminPlayerMonthView
{
    public function __construct(
        public int $playerId,
        public string $name,
        public array $stats,
        public array $events,
    ) {}
}
