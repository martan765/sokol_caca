<?php

namespace Sokol\Application\Dto;

final readonly class AttendanceSheetRow
{
    public function __construct(
        public int $userId,
        public ?int $statusId,
        public ?string $note = null,
    ) {}
}
