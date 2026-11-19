<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_knows_if_it_is_played_at_home(): void
    {
        config(['club.name' => 'Částkov']);

        $home = new Game(['home_team' => 'Částkov', 'away_team' => 'Havřice']);
        $away = new Game(['home_team' => 'Bílovice', 'away_team' => 'Částkov']);

        $this->assertTrue($home->isHome());
        $this->assertFalse($away->isHome());
        $this->assertTrue($away->isOurTeam($away->away_team));
    }

    public function test_game_cards_show_home_away_and_countdown(): void
    {
        config(['club.name' => 'Částkov']);
        Game::create(['match_type_id' => 1, 'home_team' => 'Částkov', 'away_team' => 'Havřice', 'location' => 'Částkov', 'game_date' => now()->addDay()->setTime(10, 15)]);
        Game::create(['match_type_id' => 1, 'home_team' => 'Bílovice', 'away_team' => 'Částkov', 'location' => 'Bílovice', 'game_date' => now()->addDays(10)->setTime(16, 30)]);

        $this->actingAs(User::factory()->create())->get(route('games.index'))
            ->assertOk()
            ->assertSee('Doma')
            ->assertSee('Venku')
            ->assertSee('Mistrovské utkání')
            ->assertSee('Zítra')
            ->assertSee('za 10 dní');
    }

    public function test_training_card_shows_countdown_and_description(): void
    {
        Training::create(['training_date' => now()->addDays(3)->setTime(17, 30), 'location' => 'Hřiště Částkov', 'description' => 'Standardky']);

        $this->actingAs(User::factory()->create())->get(route('trainings.index'))
            ->assertOk()
            ->assertSee('za 3 dny')
            ->assertSee('Standardky')
            ->assertSee('17:30');
    }
}
