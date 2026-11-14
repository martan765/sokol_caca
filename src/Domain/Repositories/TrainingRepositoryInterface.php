<?php

namespace Sokol\Domain\Repositories;

use DateTimeImmutable;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Pagination\Page;

interface TrainingRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Training;

    public function getById(int $id): Training;

    public function upcoming(DateTimeImmutable $from): array;

    public function all(): array;

    public function paginate(int $page, int $perPage): Page;

    public function save(Training $training): Training;

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool;
}
