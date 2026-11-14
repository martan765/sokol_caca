<?php

namespace Sokol\Domain\Entities;

use DateTimeImmutable;

final class Game
{
    public function __construct(
        private ?int $id,
        private int $gameTypeId,
        private string $homeTeam,
        private string $awayTeam,
        private DateTimeImmutable $date,
        private ?string $location = null,
        private ?string $result = null,
    ) {}

    public static function schedule(
        int $gameTypeId,
        string $homeTeam,
        string $awayTeam,
        DateTimeImmutable $date,
        ?string $location,
        ?string $result,
    ): self {
        return new self(null, $gameTypeId, $homeTeam, $awayTeam, $date, $location, $result);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function gameTypeId(): int
    {
        return $this->gameTypeId;
    }

    public function homeTeam(): string
    {
        return $this->homeTeam;
    }

    public function awayTeam(): string
    {
        return $this->awayTeam;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function result(): ?string
    {
        return $this->result;
    }

    public function label(): string
    {
        return $this->homeTeam . ' vs ' . $this->awayTeam;
    }

    public function update(
        int $gameTypeId,
        string $homeTeam,
        string $awayTeam,
        DateTimeImmutable $date,
        ?string $location,
        ?string $result,
    ): void {
        $this->gameTypeId = $gameTypeId;
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->date = $date;
        $this->location = $location;
        $this->result = $result;
    }

    public function isHomeFor(string $clubName): bool
    {
        return $this->isOurTeam($this->homeTeam, $clubName);
    }

    public function isOurTeam(string $team, string $clubName): bool
    {
        return str_contains($team, $clubName);
    }

    public function isPastAt(DateTimeImmutable $now): bool
    {
        return $this->date < $now;
    }
}
