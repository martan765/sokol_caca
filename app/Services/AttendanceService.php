<?php

namespace App\Services;

use App\Models\AttendanceStatus;
use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\AttendanceStatusRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly AttendanceStatusRepositoryInterface $statuses,
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * Uloží (nebo změní) hlas hráče na tréninku či zápasu.
     *
     * @param  array{status: int, training_id?: ?int, game_id?: ?int}  $data
     */
    public function vote(User $user, array $data): void
    {
        if (! empty($data['training_id'])) {
            $this->attendance->setTrainingStatus($user, (int) $data['training_id'], (int) $data['status']);
        } else {
            $this->attendance->setGameStatus($user, (int) $data['game_id'], (int) $data['status']);
        }
    }

    /** @return Collection<int, AttendanceStatus> */
    public function statuses(): Collection
    {
        return $this->statuses->all();
    }

    public function renameStatus(AttendanceStatus $status, array $data): void
    {
        $this->statuses->update($status, $data);
    }

    /**
     * Podklady pro formulář, ve kterém trenér zapisuje docházku za hráče.
     *
     * @return array{players: Collection, current: Collection, statuses: Collection}
     */
    public function trainingSheet(Training $training): array
    {
        return $this->sheet($this->attendance->attendanceOfTraining($training));
    }

    public function gameSheet(Game $game): array
    {
        return $this->sheet($this->attendance->attendanceOfGame($game));
    }

    /**
     * @param  array<int, array{status_id?: ?int, note?: ?string}>  $rows  user_id => řádek formuláře
     */
    public function saveTrainingSheet(Training $training, array $rows): void
    {
        $this->attendance->saveTrainingSheet($training, $this->toSheet($rows));
    }

    public function saveGameSheet(Game $game, array $rows): void
    {
        $this->attendance->saveGameSheet($game, $this->toSheet($rows));
    }

    private function sheet(Collection $current): array
    {
        return [
            'players'  => $this->users->players(),
            'current'  => $current,
            'statuses' => $this->statuses->all(),
        ];
    }

    /**
     * Z formuláře bere jen hráče (ne trenéry ani podvržená ID).
     * Hráč bez vybraného stavu = hlas se smaže.
     */
    private function toSheet(array $rows): array
    {
        return $this->users->players()->mapWithKeys(function (User $player) use ($rows) {
            $row = $rows[$player->id] ?? [];

            return [$player->id => empty($row['status_id']) ? null : [
                'status_id' => (int) $row['status_id'],
                'note'      => $row['note'] ?? null,
            ]];
        })->all();
    }
}
