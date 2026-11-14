<?php

namespace Sokol\Infrastructure\Persistence;

use Illuminate\Database\ConnectionInterface;
use Sokol\Application\Ports\UnitOfWork;

final class EloquentUnitOfWork implements UnitOfWork
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {}

    public function transactional(callable $work): mixed
    {
        return $this->connection->transaction(fn () => $work());
    }
}
