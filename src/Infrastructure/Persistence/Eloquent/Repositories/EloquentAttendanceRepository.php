<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Sokol\Domain\Repositories\AttendanceRepositoryInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\AttendanceMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameAttendanceModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingAttendanceModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

final class EloquentAttendanceRepository implements AttendanceRepositoryInterface
{
    public function setStatus(int $userId, EventType $type, int $eventId, int $statusId): void
    {
        $user = UserModel::query()->find($userId) ?? throw EntityNotFoundException::for('Uživatel', $userId);

        $relation = match ($type) {
            EventType::Training => $user->trainings(),
            EventType::Game     => $user->games(),
        };

        $relation->syncWithoutDetaching([$eventId => ['status_id' => $statusId]]);
    }

    public function ofUser(int $userId, EventType $type): array
    {
        $column = AttendanceMapper::eventColumn($type);
        $result = [];

        foreach ($this->rows($type)->where('user_id', $userId)->get() as $row) {
            $result[(int) $row->getAttribute($column)] = AttendanceMapper::toEntity($row, $type);
        }

        return $result;
    }

    public function byUser(EventType $type): array
    {
        $column = AttendanceMapper::eventColumn($type);
        $result = [];

        foreach ($this->rows($type)->get() as $row) {
            $result[(int) $row->getAttribute('user_id')][(int) $row->getAttribute($column)] = AttendanceMapper::toEntity($row, $type);
        }

        return $result;
    }

    public function ofEvent(EventType $type, int $eventId): array
    {
        $result = [];

        foreach ($this->rows($type)->where(AttendanceMapper::eventColumn($type), $eventId)->get() as $row) {
            $result[(int) $row->getAttribute('user_id')] = AttendanceMapper::toEntity($row, $type);
        }

        return $result;
    }

    public function ofEvents(EventType $type, array $eventIds): array
    {
        if ($eventIds === []) {
            return [];
        }

        $column = AttendanceMapper::eventColumn($type);
        $result = [];

        foreach ($this->rows($type)->whereIn($column, $eventIds)->orderBy('id')->get() as $row) {
            $result[(int) $row->getAttribute($column)][] = AttendanceMapper::toEntity($row, $type);
        }

        return $result;
    }

    public function saveSheet(EventType $type, int $eventId, array $sheet): void
    {
        $users = $this->usersOf($type, $eventId);

        $votes = [];
        foreach ($sheet as $userId => $attendance) {
            if ($attendance !== null) {
                $votes[$userId] = AttendanceMapper::toPivotData($attendance);
            }
        }

        $users->syncWithoutDetaching($votes);
        $users->detach(array_keys($sheet, null, true));
    }

    private function rows(EventType $type): Builder
    {
        return match ($type) {
            EventType::Training => TrainingAttendanceModel::query(),
            EventType::Game     => GameAttendanceModel::query(),
        };
    }

    private function usersOf(EventType $type, int $eventId): BelongsToMany
    {
        return match ($type) {
            EventType::Training => (TrainingModel::query()->find($eventId) ?? throw EntityNotFoundException::for('Trénink', $eventId))->users(),
            EventType::Game     => (GameModel::query()->find($eventId) ?? throw EntityNotFoundException::for('Zápas', $eventId))->users(),
        };
    }
}
