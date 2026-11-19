<?php

namespace Sokol\Domain\Exceptions;

use RuntimeException;

final class EntityNotFoundException extends RuntimeException
{
    public static function for(string $entity, int $id): self
    {
        return new self(sprintf('%s s ID %d neexistuje.', $entity, $id));
    }
}
