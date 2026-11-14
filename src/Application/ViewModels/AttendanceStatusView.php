<?php

namespace Sokol\Application\ViewModels;

use Sokol\Domain\Entities\AttendanceStatus;
use Sokol\Domain\Enums\AttendanceStatusId;

final readonly class AttendanceStatusView
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $isAttending,
        public bool $isNotAttending,
    ) {}

    public static function fromEntity(AttendanceStatus $status): self
    {
        return new self(
            $status->id(),
            $status->name(),
            $status->is(AttendanceStatusId::Attending),
            $status->is(AttendanceStatusId::NotAttending),
        );
    }
}
