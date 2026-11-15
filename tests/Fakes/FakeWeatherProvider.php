<?php

namespace Tests\Fakes;

use Sokol\Application\Dto\WeatherForecast;
use Sokol\Application\Ports\WeatherProvider;

final class FakeWeatherProvider implements WeatherProvider
{
    public function __construct(
        private readonly ?WeatherForecast $forecast = null,
    ) {}

    public function forecast(): ?WeatherForecast
    {
        return $this->forecast;
    }
}
