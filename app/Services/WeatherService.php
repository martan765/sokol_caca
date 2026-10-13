<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    public function getWeather()
    {
        return Cache::remember('team_weather', 1800, function () {
            
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => 49.1044,
                'longitude' => 17.6198,
                'timezone' => 'Europe/Berlin',
                'current' => 'temperature_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m',
                'hourly' => 'temperature_2m,wind_speed_10m,precipitation_probability,rain,snowfall,apparent_temperature',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,precipitation_sum,precipitation_probability_max',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            

            return null;
        });
    }
}