<?php

namespace Sokol\Infrastructure\Weather;

use DateTimeImmutable;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Sokol\Application\Dto\CurrentWeather;
use Sokol\Application\Dto\DailyWeather;
use Sokol\Application\Dto\WeatherForecast;
use Sokol\Application\Ports\WeatherProvider;

final class OpenMeteoWeatherProvider implements WeatherProvider
{
    private const URL       = 'https://api.open-meteo.com/v1/forecast';
    private const CACHE_KEY = 'team_weather';
    private const CACHE_TTL = 1800;

    public function forecast(): ?WeatherForecast
    {
        $data = $this->fetch();

        return $data === null ? null : $this->map($data);
    }

    private function fetch(): ?array
    {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        try {
            $response = Http::timeout(5)->get(self::URL, [
                'latitude'  => 49.1044,
                'longitude' => 17.6198,
                'timezone'  => 'Europe/Prague',
                'current'   => 'temperature_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m',
                'hourly'    => 'temperature_2m,wind_speed_10m,precipitation_probability,rain,snowfall,apparent_temperature',
                'daily'     => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,precipitation_sum,precipitation_probability_max',
            ]);
        } catch (ConnectionException $e) {
            Log::warning('Počasí: API Open-Meteo je nedostupné', ['error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Počasí: API Open-Meteo vrátilo chybu', ['status' => $response->status()]);

            return null;
        }

        // Neúspěšnou odpověď necachujeme, aby se při dalším požadavku zkusilo API znovu
        $data = $response->json();
        Cache::put(self::CACHE_KEY, $data, self::CACHE_TTL);

        return is_array($data) ? $data : null;
    }

    private function map(array $data): ?WeatherForecast
    {
        $current = $data['current'] ?? null;
        if (empty($current) || ! is_array($current)) {
            return null;
        }

        $daily = $data['daily'] ?? [];
        $days  = [];

        foreach ($daily['time'] ?? [] as $i => $day) {
            $date = $this->date($day);
            if ($date === null) {
                continue;
            }

            $days[] = new DailyWeather(
                $date,
                $this->int($daily['weather_code'][$i] ?? null),
                $this->float($daily['temperature_2m_max'][$i] ?? null),
                $this->float($daily['temperature_2m_min'][$i] ?? null),
                $this->date($daily['sunrise'][$i] ?? null),
                $this->date($daily['sunset'][$i] ?? null),
                $this->float($daily['precipitation_sum'][$i] ?? null),
                $this->int($daily['precipitation_probability_max'][$i] ?? null),
            );
        }

        return new WeatherForecast(
            new CurrentWeather(
                $this->date($current['time'] ?? null),
                $this->float($current['temperature_2m'] ?? null),
                $this->float($current['apparent_temperature'] ?? null),
                $this->float($current['precipitation'] ?? null),
                $this->int($current['weather_code'] ?? null),
                $this->float($current['wind_speed_10m'] ?? null),
            ),
            $days,
        );
    }

    private function date(mixed $value): ?DateTimeImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }

    private function float(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function int(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
