<?php

namespace Sokol\Application\ViewModels;

final readonly class GameTypeView
{
    public function __construct(
        public int $id,
        public string $name,
        public int $gamesCount = 0,
    ) {}

    public function canDelete(): bool
    {
        return $this->gamesCount === 0;
    }
}
