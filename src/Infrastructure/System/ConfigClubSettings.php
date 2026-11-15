<?php

namespace Sokol\Infrastructure\System;

use Sokol\Domain\Interfaces\Managers\ClubSettingsInterface;

final class ConfigClubSettings implements ClubSettingsInterface
{
    public function clubName(): string
    {
        return (string) config('club.name');
    }
}
