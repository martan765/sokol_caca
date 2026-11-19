<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function index(WeatherService $weatherService)
    {
        $weather = $weatherService->getWeather();

        return view('weather', compact('weather'));
    }
}
