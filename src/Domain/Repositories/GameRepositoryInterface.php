<?php

namespace Sokol\Domain\Repositories;

use DateTimeImmutable;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Pagination\Page;

interface GameRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Game;

    public function getById(int $id): Game;

    public function upcoming(DateTimeImmutable $from): array;

    public function all(): array;

    public function paginate(int $page, int $perPage): Page;

    public function save(Game $game): Game;

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool;
}
