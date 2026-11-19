<?php

namespace Sokol\Domain\Entities;

final class GameType
{
    public function __construct(
        private ?int $id,
        private string $name,
    ) {}

    public static function create(string $name): self
    {
        return new self(null, $name);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}
