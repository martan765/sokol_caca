<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class TrainingView
{
    public function __construct(
        public int $id,
        public DateTimeImmutable $date,
        public string $location,
        public ?string $description,
        public bool $isPast,
        public array $voters = [],
        public ?int $myStatusId = null,
    ) {}
}
