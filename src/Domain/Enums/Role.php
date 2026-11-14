<?php

namespace Sokol\Domain\Enums;

enum Role: string
{
    case Admin = 'admin';

    case Player = 'player';

    public function label(): string
    {
        return match ($this) {
            self::Admin  => 'Trenér',
            self::Player => 'Hráč',
        };
    }
}
