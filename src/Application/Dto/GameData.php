<?php

namespace Sokol\Application\Dto;

use DateTimeImmutable;

final readonly class GameData
{
    public function __construct(
        public int $gameTypeId,
        public string $homeTeam,
        public string $awayTeam,
        public DateTimeImmutable $date,
        public ?string $location = null,
        public ?string $result = null,
    ) {}
}
