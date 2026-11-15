<?php

namespace App\Http\Controllers;

use Sokol\Application\Abstraction\WeatherServiceInterface;

class WeatherController extends Controller
{
    public function index(WeatherServiceInterface $weatherService)
    {
        $weather = $weatherService->forecast();

        return view('weather', compact('weather'));
    }
}
