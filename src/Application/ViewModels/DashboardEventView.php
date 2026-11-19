<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class DashboardEventView
{
    public function __construct(
        public string $type,
        public int $id,
        public DateTimeImmutable $date,
        public string $label,
        public ?string $location,
        public ?int $statusId = null,
        public ?string $note = null,
    ) {}

    public function isGame(): bool
    {
        return $this->type === 'game';
    }

    public function withAttendance(?AttendanceView $attendance): self
    {
        return new self($this->type, $this->id, $this->date, $this->label, $this->location, $attendance?->statusId, $attendance?->note);
    }
}
