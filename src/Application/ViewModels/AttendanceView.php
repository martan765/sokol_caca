<?php

namespace Sokol\Application\ViewModels;

use Sokol\Domain\Entities\Attendance;

final readonly class AttendanceView
{
    public function __construct(
        public int $userId,
        public int $statusId,
        public ?string $note,
    ) {}

    public static function fromEntity(Attendance $attendance): self
    {
        return new self($attendance->userId(), $attendance->statusId(), $attendance->note());
    }
}
