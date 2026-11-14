<?php

namespace Sokol\Application\Dto;

final readonly class WeatherForecast
{
    public function __construct(
        public CurrentWeather $current,
        public array $days,
    ) {}
}
