<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Enums\EventType;

final class AttendanceMapper
{
    public static function eventColumn(EventType $type): string
    {
        return match ($type) {
            EventType::Training => 'training_id',
            EventType::Game     => 'game_id',
        };
    }

    public static function toEntity(Model $row, EventType $type): Attendance
    {
        return new Attendance(
            (int) $row->getAttribute('user_id'),
            $type,
            (int) $row->getAttribute(self::eventColumn($type)),
            (int) $row->getAttribute('status_id'),
            $row->getAttribute('note'),
        );
    }

    public static function toPivotData(Attendance $attendance): array
    {
        return [
            'status_id' => $attendance->statusId(),
            'note'      => $attendance->note(),
        ];
    }
}
