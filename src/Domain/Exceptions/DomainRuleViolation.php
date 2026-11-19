<?php

namespace Sokol\Domain\Exceptions;

use DomainException;

final class DomainRuleViolation extends DomainException
{
    public static function gameTypeInUse(int $gameTypeId): self
    {
        return new self(sprintf('Typ zápasu %d používají existující zápasy, nelze ho smazat.', $gameTypeId));
    }
}
