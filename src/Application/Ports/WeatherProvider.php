<?php

namespace Sokol\Application\Ports;

use Sokol\Application\Dto\WeatherForecast;

interface WeatherProvider
{
    public function forecast(): ?WeatherForecast;
}
