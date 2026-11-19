<?php

namespace Sokol\Domain\Interfaces\Repositories;

use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Enums\EventType;

interface AttendanceRepositoryInterface
{
    public function setStatus(int $userId, EventType $type, int $eventId, int $statusId): void;

    public function ofUser(int $userId, EventType $type): array;

    public function byUser(EventType $type): array;

    public function ofEvent(EventType $type, int $eventId): array;

    public function ofEvents(EventType $type, array $eventIds): array;

    public function saveSheet(EventType $type, int $eventId, array $sheet): void;
}
