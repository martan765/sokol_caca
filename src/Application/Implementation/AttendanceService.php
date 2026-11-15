<?php

namespace Sokol\Application\Implementation;

use Sokol\Application\Abstraction\AttendanceServiceInterface;
use Sokol\Application\Dto\AttendanceSheetRow;
use Sokol\Application\Dto\AttendanceVoteData;
use Sokol\Application\Support\Changes;
use Sokol\Application\ViewModels\AttendanceSheetView;
use Sokol\Application\ViewModels\AttendanceStatusView;
use Sokol\Application\ViewModels\AttendanceView;
use Sokol\Application\ViewModels\UserView;
use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Entities\AttendanceStatus;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Managers\UnitOfWorkInterface;
use Sokol\Domain\Interfaces\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\GameRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\UserRepositoryInterface;

class AttendanceService implements AttendanceServiceInterface
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly AttendanceStatusRepositoryInterface $statuses,
        private readonly UserRepositoryInterface $users,
        private readonly TrainingRepositoryInterface $trainings,
        private readonly GameRepositoryInterface $games,
        private readonly UnitOfWorkInterface $unitOfWork,
    ) {}

    public function vote(AttendanceVoteData $vote): void
    {
        $this->attendance->setStatus($vote->userId, $vote->eventType, $vote->eventId, $vote->statusId);
    }

    public function statuses(): array
    {
        return array_map(fn (AttendanceStatus $s) => AttendanceStatusView::fromEntity($s), $this->statuses->all());
    }

    public function getStatus(int $id): AttendanceStatusView
    {
        return AttendanceStatusView::fromEntity($this->statuses->getById($id));
    }

    public function statusExists(int $id): bool
    {
        return $this->statuses->exists($id);
    }

    public function statusNameExists(string $name, ?int $exceptId = null): bool
    {
        return $this->statuses->nameExists($name, $exceptId);
    }

    public function renameStatus(int $id, string $name): array
    {
        $status = $this->statuses->getById($id);
        $before = ['name' => $status->name()];

        $status->rename($name);
        $this->statuses->save($status);

        return Changes::between($before, ['name' => $status->name()]);
    }

    public function sheet(EventType $type, int $eventId): AttendanceSheetView
    {
        [$date, $label] = match ($type) {
            EventType::Training => [$this->trainings->getById($eventId)->date(), null],
            EventType::Game     => (function () use ($eventId) {
                $game = $this->games->getById($eventId);

                return [$game->date(), $game->label()];
            })(),
        };

        $current = [];
        foreach ($this->attendance->ofEvent($type, $eventId) as $userId => $attendance) {
            $current[$userId] = AttendanceView::fromEntity($attendance);
        }

        return new AttendanceSheetView(
            $type,
            $eventId,
            $date,
            $label,
            array_map(fn (User $u) => UserView::fromEntity($u), $this->users->players()),
            $current,
            $this->statuses(),
        );
    }

    public function saveSheet(EventType $type, int $eventId, array $rows): void
    {
        $this->unitOfWork->transactional(function () use ($type, $eventId, $rows): void {
            $this->assertEventExists($type, $eventId);

            $byUser = [];
            foreach ($rows as $row) {
                $byUser[$row->userId] = $row;
            }

            $sheet = [];
            foreach ($this->users->players() as $player) {
                $playerId = (int) $player->id();
                $row = $byUser[$playerId] ?? null;

                $sheet[$playerId] = ($row === null || $row->statusId === null)
                    ? null
                    : new Attendance($playerId, $type, $eventId, $row->statusId, $row->note);
            }

            $this->attendance->saveSheet($type, $eventId, $sheet);
        });
    }

    private function assertEventExists(EventType $type, int $eventId): void
    {
        match ($type) {
            EventType::Training => $this->trainings->getById($eventId),
            EventType::Game     => $this->games->getById($eventId),
        };
    }
}
