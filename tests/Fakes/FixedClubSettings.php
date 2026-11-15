<?php

namespace Tests\Fakes;

use Sokol\Application\Ports\ClubSettings;

final class FixedClubSettings implements ClubSettings
{
    public function __construct(
        private readonly string $name = 'Částkov',
    ) {}

    public function clubName(): string
    {
        return $this->name;
    }
}
