<?php

namespace Sokol\Application\ViewModels;

use DateTimeImmutable;
use Sokol\Application\Dto\CurrentWeather;

final readonly class WeatherView
{
    public function __construct(
        public CurrentWeather $current,
        public ?DateTimeImmutable $sunrise,
        public ?DateTimeImmutable $sunset,
        public bool $isDay,
        public array $days,
    ) {}
}
