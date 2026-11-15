<?php

namespace Sokol\Domain\Entities;

use Sokol\Domain\Enums\EventType;

final class Attendance
{
    public function __construct(
        private int $userId,
        private EventType $eventType,
        private int $eventId,
        private int $statusId,
        private ?string $note = null,
    ) {}

    public function userId(): int
    {
        return $this->userId;
    }

    public function eventType(): EventType
    {
        return $this->eventType;
    }

    public function eventId(): int
    {
        return $this->eventId;
    }

    public function statusId(): int
    {
        return $this->statusId;
    }

    public function note(): ?string
    {
        return $this->note;
    }
}
