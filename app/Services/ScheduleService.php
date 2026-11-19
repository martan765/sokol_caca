<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Training;
use App\Repositories\Contracts\GameRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use Carbon\CarbonInterface;

/**
 * Kalendář týmu – tréninky i zápasy dohromady.
 */
class ScheduleService
{
    /** Minimální rozestup mezi dvěma akcemi (přibližná délka tréninku / zápasu). */
    public const MIN_GAP_MINUTES = 120;

    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
        private readonly GameRepositoryInterface $games,
    ) {}

    /**
     * Je v okolí daného času (± MIN_GAP_MINUTES) už naplánovaný trénink nebo zápas?
     *
     * @param  Training|Game|null  $except  upravovaná akce, která se sama se sebou nekryje
     */
    public function hasConflict(CarbonInterface $at, Training|Game|null $except = null): bool
    {
        $from = $at->copy()->subMinutes(self::MIN_GAP_MINUTES);
        $to   = $at->copy()->addMinutes(self::MIN_GAP_MINUTES);

        return $this->trainings->existsBetween($from, $to, $except instanceof Training ? $except->id : null)
            || $this->games->existsBetween($from, $to, $except instanceof Game ? $except->id : null);
    }
}
