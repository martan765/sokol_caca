<?php

namespace Sokol\Domain\Weather;

final readonly class WeatherForecast
{
    public function __construct(
        public CurrentWeather $current,
        public array $days,
    ) {}
}
