<?php

namespace Sokol\Application\Dto;

use DateTimeImmutable;

final readonly class DailyWeather
{
    public function __construct(
        public DateTimeImmutable $date,
        public ?int $weatherCode,
        public ?float $temperatureMax,
        public ?float $temperatureMin,
        public ?DateTimeImmutable $sunrise,
        public ?DateTimeImmutable $sunset,
        public ?float $precipitationSum,
        public ?int $precipitationProbability,
    ) {}
}
