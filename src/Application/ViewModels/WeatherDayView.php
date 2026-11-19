<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;

final readonly class WeatherDayView
{
    public const RAINY_FROM = 60;

    public function __construct(
        public DateTimeImmutable $date,
        public bool $isToday,
        public bool $isTomorrow,
        public ?int $weatherCode,
        public ?float $temperatureMax,
        public ?float $temperatureMin,
        public ?float $precipitationSum,
        public ?int $precipitationProbability,
    ) {}

    public function isRainy(): bool
    {
        return $this->precipitationProbability !== null && $this->precipitationProbability >= self::RAINY_FROM;
    }
}
