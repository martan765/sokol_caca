<?php

namespace Sokol\Infrastructure\System;

use Sokol\Application\Ports\ClubSettings;

final class ConfigClubSettings implements ClubSettings
{
    public function clubName(): string
    {
        return (string) config('club.name');
    }
}
