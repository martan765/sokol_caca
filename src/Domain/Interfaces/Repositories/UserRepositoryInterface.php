<?php

namespace Sokol\Domain\Interfaces\Repositories;

use Sokol\Domain\Entities\User;
use Sokol\Domain\Pagination\Page;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?User;

    public function getById(int $id): User;

    public function findByIds(array $ids): array;

    public function emailExists(string $email, ?int $exceptId = null): bool;

    public function players(): array;

    public function paginate(int $page, int $perPage): Page;

    public function save(User $user): User;
}
