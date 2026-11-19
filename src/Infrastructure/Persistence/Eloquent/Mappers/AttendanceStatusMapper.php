<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use Sokol\Domain\Entities\AttendanceStatus;
use Sokol\Infrastructure\Persistence\Eloquent\Models\AttendanceStatusModel;

final class AttendanceStatusMapper
{
    public static function toEntity(AttendanceStatusModel $model): AttendanceStatus
    {
        return new AttendanceStatus((int) $model->id, (string) $model->name);
    }

    public static function fill(AttendanceStatusModel $model, AttendanceStatus $status): AttendanceStatusModel
    {
        return $model->forceFill(['id' => $status->id(), 'name' => $status->name()]);
    }
}
