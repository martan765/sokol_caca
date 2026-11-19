<?php

namespace Sokol\Domain\Interfaces\Repositories;

use Sokol\Domain\Entities\AttendanceStatus;

interface AttendanceStatusRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?AttendanceStatus;

    public function getById(int $id): AttendanceStatus;

    public function all(): array;

    public function nameExists(string $name, ?int $exceptId = null): bool;

    public function save(AttendanceStatus $status): AttendanceStatus;
}
