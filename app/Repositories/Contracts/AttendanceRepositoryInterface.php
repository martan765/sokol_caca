<?php

namespace App\Repositories\Contracts;

use App\Models\Game;
use App\Models\GameAttendance;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Hlasování hráčů o účasti (entity TrainingAttendance / GameAttendance).
 */
interface AttendanceRepositoryInterface
{
    /** Vytvoří nebo přepíše hlas hráče – jeden hráč má u akce vždy nejvýše jeden hlas. */
    public function setTrainingStatus(User $user, int $trainingId, int $statusId): void;

    public function setGameStatus(User $user, int $gameId, int $statusId): void;

    /** @return Collection<int, TrainingAttendance> training_id => účast */
    public function trainingAttendanceOf(User $user): Collection;

    /** @return Collection<int, GameAttendance> game_id => účast */
    public function gameAttendanceOf(User $user): Collection;

    /** @return Collection<int, Collection<int, TrainingAttendance>> user_id => (training_id => účast) */
    public function trainingAttendanceByUser(): Collection;

    /** @return Collection<int, Collection<int, GameAttendance>> user_id => (game_id => účast) */
    public function gameAttendanceByUser(): Collection;

    /** @return Collection<int, TrainingAttendance> user_id => účast na daném tréninku */
    public function attendanceOfTraining(Training $training): Collection;

    /** @return Collection<int, GameAttendance> user_id => účast na daném zápase */
    public function attendanceOfGame(Game $game): Collection;

    /**
     * Uloží docházku celé akce najednou.
     *
     * @param  array<int, array{status_id: int, note: ?string}|null>  $sheet  user_id => účast, null = hlas smazat
     */
    public function saveTrainingSheet(Training $training, array $sheet): void;

    /** @param  array<int, array{status_id: int, note: ?string}|null>  $sheet */
    public function saveGameSheet(Game $game, array $sheet): void;
}
