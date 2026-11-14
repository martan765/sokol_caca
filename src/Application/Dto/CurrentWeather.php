<?php

namespace Sokol\Application\Dto;

use DateTimeImmutable;

final readonly class CurrentWeather
{
    public function __construct(
        public ?DateTimeImmutable $time,
        public ?float $temperature,
        public ?float $apparentTemperature,
        public ?float $precipitation,
        public ?int $weatherCode,
        public ?float $windSpeed,
    ) {}
}
