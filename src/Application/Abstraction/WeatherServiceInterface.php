<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\ViewModels\WeatherView;

interface WeatherServiceInterface
{
    public function forecast(): ?WeatherView;
}
