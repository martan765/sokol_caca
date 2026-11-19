<?php

namespace Sokol\Domain\Interfaces\Managers;

use Sokol\Domain\Weather\WeatherForecast;

interface WeatherProviderInterface
{
    public function forecast(): ?WeatherForecast;
}
