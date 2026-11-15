<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\AttendanceStatus;
use Sokol\Domain\Interfaces\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\AttendanceStatusMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\AttendanceStatusModel;

final class EloquentAttendanceStatusRepository extends EloquentRepository implements AttendanceStatusRepositoryInterface
{
    protected function modelClass(): string
    {
        return AttendanceStatusModel::class;
    }

    protected function entityName(): string
    {
        return 'Stav účasti';
    }

    protected function toEntity(Model $model): AttendanceStatus
    {
        return AttendanceStatusMapper::toEntity($model);
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
        return $this->toEntities($this->query()->orderBy('id'));
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        return $this->query()
            ->where('name', $name)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }

    public function save(AttendanceStatus $status): AttendanceStatus
    {
        $model = $this->query()->find($status->id()) ?? new AttendanceStatusModel();

        AttendanceStatusMapper::fill($model, $status)->save();

        return AttendanceStatusMapper::toEntity($model);
    }
}
