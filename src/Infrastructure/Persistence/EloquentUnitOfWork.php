<?php

namespace Sokol\Infrastructure\Persistence;

use Illuminate\Database\ConnectionInterface;
use Sokol\Domain\Interfaces\Managers\UnitOfWorkInterface;

final class EloquentUnitOfWork implements UnitOfWorkInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {}

    public function transactional(callable $work): mixed
    {
        return $this->connection->transaction(fn () => $work());
    }
}
