<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sokol\Domain\Entities\Game;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingModel;
use Tests\TestCase;

class EventCardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_knows_if_it_is_played_at_home(): void
    {
        $home = new Game(null, 1, 'Částkov', 'Havřice', new DateTimeImmutable());
        $away = new Game(null, 1, 'Bílovice', 'Částkov', new DateTimeImmutable());

        $this->assertTrue($home->isHomeFor('Částkov'));
        $this->assertFalse($away->isHomeFor('Částkov'));
        $this->assertTrue($away->isOurTeam($away->awayTeam(), 'Částkov'));
    }

    public function test_game_cards_show_home_away_and_countdown(): void
    {
        config(['club.name' => 'Částkov']);
        GameModel::create(['match_type_id' => 1, 'home_team' => 'Částkov', 'away_team' => 'Havřice', 'location' => 'Částkov', 'game_date' => now()->addDay()->setTime(10, 15)]);
        GameModel::create(['match_type_id' => 1, 'home_team' => 'Bílovice', 'away_team' => 'Částkov', 'location' => 'Bílovice', 'game_date' => now()->addDays(10)->setTime(16, 30)]);

        $this->actingAs(UserFactory::new()->create())->get(route('games.index'))
            ->assertOk()
            ->assertSee('Doma')
            ->assertSee('Venku')
            ->assertSee('Mistrovské utkání')
            ->assertSee('Zítra')
            ->assertSee('za 10 dní');
    }

    public function test_training_card_shows_countdown_and_description(): void
    {
        TrainingModel::create(['training_date' => now()->addDays(3)->setTime(17, 30), 'location' => 'Hřiště Částkov', 'description' => 'Standardky']);

        $this->actingAs(UserFactory::new()->create())->get(route('trainings.index'))
            ->assertOk()
            ->assertSee('za 3 dny')
            ->assertSee('Standardky')
            ->assertSee('17:30');
    }
}
