<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private const CACHE_KEY = 'team_weather';
    private const CACHE_TTL = 1800;

    public function getWeather(): ?array
    {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        try {
            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
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

        return $data;
    }
}
