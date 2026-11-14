<?php

namespace Sokol\Domain\Repositories;

use Sokol\Domain\Entities\GameType;

interface GameTypeRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?GameType;

    public function getById(int $id): GameType;

    public function all(): array;

    public function gameCounts(): array;

    /** Používá typ nějaký zápas? */
    public function isUsed(int $id): bool;

    public function nameExists(string $name, ?int $exceptId = null): bool;

    public function save(GameType $type): GameType;
}
