<?php

namespace Tests\Fakes;

use Sokol\Domain\Interfaces\Managers\ClubSettingsInterface;

final class FixedClubSettings implements ClubSettingsInterface
{
    public function __construct(
        private readonly string $name = 'Částkov',
    ) {}

    public function clubName(): string
    {
        return $this->name;
    }
}
