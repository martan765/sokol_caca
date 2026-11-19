<?php

namespace App\Services;

use App\Models\AttendanceStatus;
use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\GameRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Měsíční přehled akcí a docházkové statistiky pro dashboard.
 */
class DashboardService
{
    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
        private readonly GameRepositoryInterface $games,
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function forPlayer(User $user): array
    {
        $trainAtt = $this->attendance->trainingAttendanceOf($user);
        $gameAtt  = $this->attendance->gameAttendanceOf($user);

        $grouped = $this->eventsByMonth()->map(fn (Collection $events) => $events->map(function (array $event) use ($trainAtt, $gameAtt) {
            $att = ($event['type'] === 'training' ? $trainAtt : $gameAtt)->get($event['id']);

            return $event + [
                'status_id' => $att?->status_id,
                'note'      => $att?->note,
            ];
        }));

        $monthlyStats = $grouped->map(fn (Collection $events) => $this->monthStats($events, fn (array $e) => $e['status_id']));

        return [
            'grouped'      => $grouped,
            'monthlyStats' => $monthlyStats,
        ];
    }

    public function forAdmin(): array
    {
        $players  = $this->users->players();
        $trainAtt = $this->attendance->trainingAttendanceByUser();
        $gameAtt  = $this->attendance->gameAttendanceByUser();

        $eventsByMonth = $this->eventsByMonth();

        $playerMonthlyStats = [];
        foreach ($players as $player) {
            $statusOf = fn (array $e) => ($e['type'] === 'training' ? $trainAtt : $gameAtt)
                ->get($player->id)?->get($e['id'])?->status_id;

            foreach ($eventsByMonth as $month => $events) {
                $playerMonthlyStats[$player->id][$month] = $this->monthStats($events, $statusOf);
            }
        }

        return [
            'players'            => $players,
            'eventsByMonth'      => $eventsByMonth,
            'months'             => $eventsByMonth->keys(),
            'playerMonthlyStats' => $playerMonthlyStats,
            'trainAtt'           => $trainAtt,
            'gameAtt'            => $gameAtt,
        ];
    }

    /**
     * Všechny tréninky a zápasy seřazené od nejnovějších, seskupené podle měsíce (Y-m).
     */
    private function eventsByMonth(): Collection
    {
        $trainings = $this->trainings->all()->map(fn (Training $t) => [
            'type'     => 'training',
            'id'       => $t->id,
            'date'     => $t->training_date,
            'label'    => 'Trénink',
            'location' => $t->location,
        ]);

        $games = $this->games->all()->map(fn (Game $g) => [
            'type'     => 'game',
            'id'       => $g->id,
            'date'     => $g->game_date,
            'label'    => $g->home_team . ' vs ' . $g->away_team,
            'location' => $g->location,
        ]);

        return $trainings->concat($games)
            ->sortByDesc('date')
            ->groupBy(fn (array $e) => $e['date']->format('Y-m'));
    }

    /**
     * @param  callable(array): ?int  $statusOf  vrací status_id hráče pro danou akci
     */
    private function monthStats(Collection $events, callable $statusOf): array
    {
        $stats = [];

        foreach (['training' => 'trainings', 'game' => 'games'] as $type => $key) {
            $ofType   = $events->where('type', $type);
            $total    = $ofType->count();
            $attended = $ofType->filter(fn (array $e) => $statusOf($e) == AttendanceStatus::ATTENDING)->count();

            $stats[$key] = [
                'total'      => $total,
                'attended'   => $attended,
                'percentage' => $total > 0 ? (int) round($attended / $total * 100) : 0,
            ];
        }

        return $stats;
    }
}
