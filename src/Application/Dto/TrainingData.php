<?php

namespace Sokol\Application\Dto;

use DateTimeImmutable;

final readonly class TrainingData
{
    public function __construct(
        public DateTimeImmutable $date,
        public string $location,
        public ?string $description = null,
    ) {}
}
