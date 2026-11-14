<?php

namespace Sokol\Domain\Entities;

use DateTimeImmutable;

final class Training
{
    public function __construct(
        private ?int $id,
        private DateTimeImmutable $date,
        private string $location,
        private ?string $description = null,
    ) {}

    public static function plan(DateTimeImmutable $date, string $location, ?string $description): self
    {
        return new self(null, $date, $location, $description);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function location(): string
    {
        return $this->location;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function update(DateTimeImmutable $date, string $location, ?string $description): void
    {
        $this->date = $date;
        $this->location = $location;
        $this->description = $description;
    }

    public function isPastAt(DateTimeImmutable $now): bool
    {
        return $this->date < $now;
    }
}
