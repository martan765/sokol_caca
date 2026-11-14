<?php

namespace Sokol\Domain\Repositories;

use Sokol\Domain\Exceptions\EntityNotFoundException;

interface RepositoryInterface
{
    public function findById(int $id): ?object;

    public function getById(int $id): object;

    public function exists(int $id): bool;

    public function delete(int $id): void;
}
