<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_vote_and_change_vote_without_duplicates(): void
    {
        $player   = User::factory()->create();
        $training = Training::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);

        $this->actingAs($player)->post(route('attendance.store'), ['training_id' => $training->id, 'status' => 1])
            ->assertSessionHas('success');
        $this->actingAs($player)->post(route('attendance.store'), ['training_id' => $training->id, 'status' => 2]);

        $this->assertDatabaseCount('training_attendance', 1);
        $this->assertDatabaseHas('training_attendance', [
            'user_id' => $player->id, 'training_id' => $training->id, 'status_id' => 2,
        ]);
    }

    public function test_vote_for_game(): void
    {
        $player = User::factory()->create();
        $game   = Game::create([
            'match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDay(),
        ]);

        $this->actingAs($player)->post(route('attendance.store'), ['game_id' => $game->id, 'status' => 1]);

        $this->assertDatabaseHas('game_attendance', ['user_id' => $player->id, 'game_id' => $game->id, 'status_id' => 1]);
    }

    public function test_vote_requires_event_and_valid_status(): void
    {
        $player = User::factory()->create();

        $this->actingAs($player)->post(route('attendance.store'), ['status' => 1])
            ->assertSessionHasErrors(['training_id', 'game_id']);

        $training = Training::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);
        $this->actingAs($player)->post(route('attendance.store'), ['training_id' => $training->id, 'status' => 99])
            ->assertSessionHasErrors('status');
    }

    public function test_deleting_user_removes_game_attendance(): void
    {
        $player = User::factory()->create();
        $game   = Game::create([
            'match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDay(),
        ]);
        $player->games()->attach($game->id, ['status_id' => 1]);

        $player->delete();

        $this->assertDatabaseCount('game_attendance', 0);
    }

    public function test_listing_pages_render(): void
    {
        $player = User::factory()->create();
        Training::create(['training_date' => now()->addDay(), 'location' => 'Umělka']);

        $this->actingAs($player)->get(route('trainings.index'))->assertOk()->assertSee('Umělka');
        $this->actingAs($player)->get(route('games.index'))->assertOk()->assertSee('Žádné zápasy.');
    }
}
