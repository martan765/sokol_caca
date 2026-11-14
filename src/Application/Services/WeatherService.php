<?php

namespace Sokol\Application\Services;

use Sokol\Application\Dto\DailyWeather;
use Sokol\Application\Ports\Clock;
use Sokol\Application\Ports\WeatherProvider;
use Sokol\Application\ViewModels\WeatherDayView;
use Sokol\Application\ViewModels\WeatherView;

class WeatherService
{
    public function __construct(
        private readonly WeatherProvider $provider,
        private readonly Clock $clock,
    ) {}

    public function forecast(): ?WeatherView
    {
        $forecast = $this->provider->forecast();
        if ($forecast === null) {
            return null;
        }

        $now      = $this->clock->now();
        $today    = $now->format('Y-m-d');
        $tomorrow = $now->modify('+1 day')->format('Y-m-d');

        $first   = $forecast->days[0] ?? null;
        $sunrise = $first?->sunrise;
        $sunset  = $first?->sunset;
        $time    = $forecast->current->time ?? $now;

        $days = array_map(fn (DailyWeather $d) => new WeatherDayView(
            $d->date,
            $d->date->format('Y-m-d') === $today,
            $d->date->format('Y-m-d') === $tomorrow,
            $d->weatherCode,
            $d->temperatureMax,
            $d->temperatureMin,
            $d->precipitationSum,
            $d->precipitationProbability,
        ), $forecast->days);

        return new WeatherView(
            $forecast->current,
            $sunrise,
            $sunset,
            ! $sunrise || ! $sunset || ($time >= $sunrise && $time <= $sunset),
            $days,
        );
    }
}
