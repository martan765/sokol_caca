<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;
use Sokol\Domain\Enums\EventType;

final readonly class AttendanceSheetView
{
    public function __construct(
        public EventType $eventType,
        public int $eventId,
        public DateTimeImmutable $eventDate,
        public ?string $eventLabel,
        public array $players,
        public array $current,
        public array $statuses,
    ) {}
}
