<?php

namespace Sokol\Application\Dto;

use Sokol\Domain\Enums\EventType;

final readonly class AttendanceVoteData
{
    public function __construct(
        public int $userId,
        public EventType $eventType,
        public int $eventId,
        public int $statusId,
    ) {}
}
