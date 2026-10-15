<?php

namespace App\Repositories\Eloquent;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Účast je uložená v pivot tabulkách training_attendance a game_attendance,
 * ven proto vracíme pivot modely TrainingAttendance / GameAttendance.
 */
class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function setTrainingStatus(User $user, int $trainingId, int $statusId): void
    {
        $user->trainings()->syncWithoutDetaching([$trainingId => ['status_id' => $statusId]]);
    }

    public function setGameStatus(User $user, int $gameId, int $statusId): void
    {
        $user->games()->syncWithoutDetaching([$gameId => ['status_id' => $statusId]]);
    }

    public function trainingAttendanceOf(User $user): Collection
    {
        return $user->trainings()->get()->mapWithKeys(fn (Training $t) => [$t->id => $t->pivot]);
    }

    public function gameAttendanceOf(User $user): Collection
    {
        return $user->games()->get()->mapWithKeys(fn (Game $g) => [$g->id => $g->pivot]);
    }

    public function trainingAttendanceByUser(): Collection
    {
        return User::with('trainings')->get()->mapWithKeys(fn (User $u) => [
            $u->id => $u->trainings->mapWithKeys(fn (Training $t) => [$t->id => $t->pivot]),
        ]);
    }

    public function gameAttendanceByUser(): Collection
    {
        return User::with('games')->get()->mapWithKeys(fn (User $u) => [
            $u->id => $u->games->mapWithKeys(fn (Game $g) => [$g->id => $g->pivot]),
        ]);
    }

    public function attendanceOfTraining(Training $training): Collection
    {
        return $training->users()->get()->mapWithKeys(fn (User $u) => [$u->id => $u->pivot]);
    }

    public function attendanceOfGame(Game $game): Collection
    {
        return $game->users()->get()->mapWithKeys(fn (User $u) => [$u->id => $u->pivot]);
    }

    public function saveTrainingSheet(Training $training, array $sheet): void
    {
        $this->saveSheet($training->users(), $sheet);
    }

    public function saveGameSheet(Game $game, array $sheet): void
    {
        $this->saveSheet($game->users(), $sheet);
    }

    /**
     * Hlasy uživatelů, kteří v $sheet nejsou, zůstávají beze změny.
     */
    private function saveSheet(BelongsToMany $users, array $sheet): void
    {
        $users->syncWithoutDetaching(array_filter($sheet));
        $users->detach(array_keys($sheet, null, true));
    }
}
