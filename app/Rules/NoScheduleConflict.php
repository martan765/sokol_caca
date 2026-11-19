<?php

namespace App\Rules;

use App\Models\Game;
use App\Models\Training;
use App\Services\ScheduleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

/**
 * Tým nemůže mít dvě akce (trénink / zápas) naráz – mezi nimi musí být
 * alespoň ScheduleService::MIN_GAP_MINUTES minut.
 *
 * Pravidlo předpokládá platné datum, proto ho v rules() dávejte za 'bail' a 'date'.
 */
class NoScheduleConflict implements ValidationRule
{
    private Training|Game|null $except = null;

    public function __construct(
        private readonly ScheduleService $schedule,
    ) {}

    /**
     * Při úpravě akce se nemá kontrolovat kolize s ní samotnou.
     */
    public function ignoring(Training|Game|null $event): static
    {
        $this->except = $event;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->schedule->hasConflict(Carbon::parse($value), $this->except)) {
            $hours = ScheduleService::MIN_GAP_MINUTES / 60;

            $fail("V tomto čase už je naplánovaný jiný trénink nebo zápas. Mezi akcemi musí být alespoň {$hours} hodiny.");
        }
    }
}
