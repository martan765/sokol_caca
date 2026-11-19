<?php

namespace Tests\Fakes;

use Sokol\Domain\Interfaces\Managers\WeatherProviderInterface;
use Sokol\Domain\Weather\WeatherForecast;

final class FakeWeatherProvider implements WeatherProviderInterface
{
    public function __construct(
        private readonly ?WeatherForecast $forecast = null,
    ) {}

    public function forecast(): ?WeatherForecast
    {
        return $this->forecast;
    }
}
