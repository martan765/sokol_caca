<?php

namespace Sokol\Domain\Enums;

enum EventType: string
{
    case Training = 'training';
    case Game = 'game';

    public function label(): string
    {
        return match ($this) {
            self::Training => 'Trénink',
            self::Game     => 'Zápas',
        };
    }
}
