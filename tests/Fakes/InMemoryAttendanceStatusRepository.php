<?php

namespace Tests\Fakes;

use Sokol\Domain\Entities\AttendanceStatus;
use Sokol\Domain\Enums\AttendanceStatusId;
use Sokol\Domain\Interfaces\Repositories\AttendanceStatusRepositoryInterface;

final class InMemoryAttendanceStatusRepository extends InMemoryRepository implements AttendanceStatusRepositoryInterface
{
    public function __construct()
    {
        $this->store(new AttendanceStatus(AttendanceStatusId::Attending->value, 'Budu'));
        $this->store(new AttendanceStatus(AttendanceStatusId::NotAttending->value, 'Nebudu'));
    }

    protected function entityName(): string
    {
        return 'Stav účasti';
    }

    public function findById(int $id): ?AttendanceStatus
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): AttendanceStatus
    {
        return $this->getEntity($id);
    }

    public function all(): array
    {
        $all = $this->items;
        ksort($all);

        return array_values(array_map(fn (AttendanceStatus $s) => clone $s, $all));
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        foreach ($this->items as $id => $status) {
            if ($status->name() === $name && $id !== $exceptId) {
                return true;
            }
        }

        return false;
    }

    public function save(AttendanceStatus $status): AttendanceStatus
    {
        return $this->store($status);
    }
}
