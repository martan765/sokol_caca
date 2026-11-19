<?php

namespace Sokol\Domain\Entities;

use Sokol\Domain\Enums\AttendanceStatusId;

final class AttendanceStatus
{
    public function __construct(
        private int $id,
        private string $name,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function is(AttendanceStatusId $status): bool
    {
        return $this->id === $status->value;
    }
}
