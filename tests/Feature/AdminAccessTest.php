<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.trainings.create'))->assertRedirect(route('login'));
    }

    public function test_player_cannot_access_admin_pages(): void
    {
        $player = User::factory()->create();

        $this->actingAs($player)->get(route('admin.trainings.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($player)->get(route('admin.games.create'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_player_cannot_create_or_delete_events(): void
    {
        $player   = User::factory()->create();
        $training = Training::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);

        $this->actingAs($player)->post(route('admin.trainings.store'), [
            'training_date' => now()->addDays(2)->toDateTimeString(),
            'location'      => 'Hřiště',
        ]);
        $this->actingAs($player)->put(route('admin.trainings.update', $training), [
            'training_date' => now()->addDays(5)->toDateTimeString(),
            'location'      => 'Jinde',
        ]);
        $this->actingAs($player)->delete(route('admin.trainings.destroy', $training));

        $this->assertDatabaseCount('trainings', 1);
        $this->assertDatabaseHas('trainings', ['id' => $training->id, 'location' => 'Hřiště']);
    }

    public function test_player_receives_403_on_json_request(): void
    {
        $player = User::factory()->create();

        $this->actingAs($player)->getJson(route('admin.games.create'))->assertForbidden();
    }

    public function test_admin_can_manage_trainings_and_games(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.trainings.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.games.create'))->assertOk();

        $this->actingAs($admin)->post(route('admin.trainings.store'), [
            'training_date' => now()->addDay()->toDateTimeString(),
            'location'      => 'Hřiště Částkov',
        ])->assertRedirect(route('admin.trainings.index'));

        $this->actingAs($admin)->post(route('admin.games.store'), [
            'game_date'     => now()->addDays(3)->toDateTimeString(),
            'home_team'     => 'Částkov',
            'away_team'     => 'Havřice',
            'location'      => 'Částkov',
            'match_type_id' => 1,
        ])->assertRedirect(route('admin.games.index'));

        $this->actingAs($admin)->get(route('admin.trainings.index'))->assertOk()->assertSee('Hřiště Částkov');
        $this->actingAs($admin)->get(route('admin.games.index'))->assertOk()->assertSee('Částkov vs Havřice');

        $game = Game::firstOrFail();
        $this->actingAs($admin)->delete(route('admin.games.destroy', $game))->assertRedirect(route('admin.games.index'));

        $this->assertDatabaseCount('trainings', 1);
        $this->assertDatabaseCount('games', 0);
    }

    public function test_admin_can_edit_training(): void
    {
        $admin    = User::factory()->admin()->create();
        $at       = now()->addDays(2)->setTime(17, 30);
        $training = Training::create(['training_date' => $at, 'location' => 'Hřiště']);

        $this->actingAs($admin)->get(route('admin.trainings.edit', $training))
            ->assertOk()
            ->assertSee($at->format('Y-m-d\TH:i'));

        // Posun o 30 minut – kolize sám se sebou se nepočítá
        $this->actingAs($admin)->put(route('admin.trainings.update', $training), [
            'training_date' => $at->copy()->addMinutes(30)->toDateTimeString(),
            'location'      => 'Umělka Zlámanec',
            'description'   => 'Střelba',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.trainings.index'));

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id, 'location' => 'Umělka Zlámanec', 'description' => 'Střelba',
        ]);
    }

    public function test_admin_can_edit_game_and_fill_in_result(): void
    {
        $admin = User::factory()->admin()->create();
        $game  = Game::create([
            'match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'location' => 'X', 'game_date' => now()->subDay(),
        ]);

        $this->actingAs($admin)->get(route('admin.games.edit', $game))->assertOk();

        $this->actingAs($admin)->put(route('admin.games.update', $game), [
            'game_date'     => $game->game_date->toDateTimeString(),
            'home_team'     => 'A',
            'away_team'     => 'B',
            'location'      => 'X',
            'match_type_id' => 1,
            'result'        => '3:1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.games.index'));

        $this->assertDatabaseHas('games', ['id' => $game->id, 'result' => '3:1']);
    }

    public function test_dashboards_render(): void
    {
        $training = Training::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);
        $player   = User::factory()->create();
        $player->trainings()->attach($training->id, ['status_id' => 1]);

        $this->actingAs($player)->get(route('dashboard'))->assertOk()->assertSee('100%');
        $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'))->assertOk()->assertSee($player->name);
    }
}
