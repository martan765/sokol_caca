<?php

namespace Tests\Fakes;

use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Repositories\AttendanceRepositoryInterface;

final class InMemoryAttendanceRepository implements AttendanceRepositoryInterface
{
    private array $votes = [];

    public function setStatus(int $userId, EventType $type, int $eventId, int $statusId): void
    {
        $note = ($this->votes[$type->value][$eventId][$userId] ?? null)?->note();

        $this->votes[$type->value][$eventId][$userId] = new Attendance($userId, $type, $eventId, $statusId, $note);
    }

    public function ofUser(int $userId, EventType $type): array
    {
        $result = [];
        foreach ($this->votes[$type->value] ?? [] as $eventId => $votes) {
            if (isset($votes[$userId])) {
                $result[$eventId] = $votes[$userId];
            }
        }

        return $result;
    }

    public function byUser(EventType $type): array
    {
        $result = [];
        foreach ($this->votes[$type->value] ?? [] as $eventId => $votes) {
            foreach ($votes as $userId => $vote) {
                $result[$userId][$eventId] = $vote;
            }
        }

        return $result;
    }

    public function ofEvent(EventType $type, int $eventId): array
    {
        return $this->votes[$type->value][$eventId] ?? [];
    }

    public function ofEvents(EventType $type, array $eventIds): array
    {
        $result = [];
        foreach ($eventIds as $eventId) {
            if (! empty($this->votes[$type->value][$eventId])) {
                $result[$eventId] = array_values($this->votes[$type->value][$eventId]);
            }
        }

        return $result;
    }

    public function saveSheet(EventType $type, int $eventId, array $sheet): void
    {
        foreach ($sheet as $userId => $attendance) {
            if ($attendance === null) {
                unset($this->votes[$type->value][$eventId][$userId]);
            } else {
                $this->votes[$type->value][$eventId][$userId] = $attendance;
            }
        }
    }
}
