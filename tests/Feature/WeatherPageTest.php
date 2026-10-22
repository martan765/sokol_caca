<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_weather_page_shows_current_conditions_and_forecast(): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response([
            'current' => [
                'time' => '2026-10-22T12:00', 'temperature_2m' => 6.5, 'apparent_temperature' => 4.7,
                'precipitation' => 0, 'weather_code' => 0, 'wind_speed_10m' => 2.6,
            ],
            'daily' => [
                'time'                          => ['2026-10-22', '2026-10-23'],
                'weather_code'                  => [63, 45],
                'temperature_2m_max'            => [14.5, 13.5],
                'temperature_2m_min'            => [4.3, 1.8],
                'sunrise'                       => ['2026-10-22T07:20', '2026-10-23T07:22'],
                'sunset'                        => ['2026-10-22T17:46', '2026-10-23T17:44'],
                'precipitation_sum'             => [23.5, 0],
                'precipitation_probability_max' => [100, 5],
            ],
        ])]);

        $this->actingAs(User::factory()->create())->get(route('weather.index'))
            ->assertOk()
            ->assertSee('6,5')
            ->assertSee('Jasno')
            ->assertSee('Pocitově 4,7 °C')
            ->assertSee('07:20')
            ->assertSee('Déšť')
            ->assertSee('100 %');
    }

    public function test_weather_page_handles_api_outage(): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response(null, 500)]);

        $this->actingAs(User::factory()->create())->get(route('weather.index'))
            ->assertOk()
            ->assertSee('Nepodařilo se načíst data o počasí.');
    }
}
