<?php

namespace App\Rules;

use Closure;
use DateTimeImmutable;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Sokol\Application\Services\ScheduleService;
use Sokol\Domain\Enums\EventType;

/**
 * Tým nemůže mít dvě akce (trénink / zápas) naráz – mezi nimi musí být
 * alespoň ScheduleRules::MIN_GAP_MINUTES minut.
 *
 * Pravidlo předpokládá platné datum, proto ho v rules() dávejte za 'bail' a 'date'.
 */
class NoScheduleConflict implements ValidationRule
{
    private ?EventType $exceptType = null;

    private ?int $exceptId = null;

    public function __construct(
        private readonly ScheduleService $schedule,
    ) {}

    /**
     * Při úpravě akce se nemá kontrolovat kolize s ní samotnou.
     */
    public function ignoring(?int $eventId, EventType $type): static
    {
        $this->exceptId   = $eventId;
        $this->exceptType = $eventId === null ? null : $type;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $at = new DateTimeImmutable((string) $value);
        } catch (Exception) {
            return;
        }

        if ($this->schedule->hasConflict($at, $this->exceptType, $this->exceptId)) {
            $hours = $this->schedule->minGapHours();

            $fail("V tomto čase už je naplánovaný jiný trénink nebo zápas. Mezi akcemi musí být alespoň {$hours} hodiny.");
        }
    }
}
