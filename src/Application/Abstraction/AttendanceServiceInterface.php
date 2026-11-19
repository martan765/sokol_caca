<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\Dto\AttendanceVoteData;
use Sokol\Application\ViewModels\AttendanceSheetView;
use Sokol\Application\ViewModels\AttendanceStatusView;
use Sokol\Domain\Enums\EventType;

interface AttendanceServiceInterface
{
    public function vote(AttendanceVoteData $vote): void;

    public function statuses(): array;

    public function getStatus(int $id): AttendanceStatusView;

    public function statusExists(int $id): bool;

    public function statusNameExists(string $name, ?int $exceptId = null): bool;

    public function renameStatus(int $id, string $name): array;

    public function sheet(EventType $type, int $eventId): AttendanceSheetView;

    public function saveSheet(EventType $type, int $eventId, array $rows): void;
}
