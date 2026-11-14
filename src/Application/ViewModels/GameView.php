<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class GameView
{
    public function __construct(
        public int $id,
        public int $gameTypeId,
        public ?string $typeName,
        public string $homeTeam,
        public string $awayTeam,
        public string $label,
        public DateTimeImmutable $date,
        public ?string $location,
        public ?string $result,
        public bool $isHome,
        public bool $homeIsOurs,
        public bool $awayIsOurs,
        public bool $isPast,
        public array $voters = [],
        public ?int $myStatusId = null,
    ) {}
}
