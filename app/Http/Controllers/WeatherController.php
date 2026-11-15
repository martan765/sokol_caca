<?php

namespace App\Http\Controllers;

use Sokol\Application\Services\WeatherService;

class WeatherController extends Controller
{
    public function index(WeatherService $weatherService)
    {
        $weather = $weatherService->forecast();

        return view('weather', compact('weather'));
    }
}
