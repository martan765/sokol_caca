<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\CarbonInterface;
use Tests\TestCase;

class ScheduleConflictTest extends TestCase
{
    use RefreshDatabase;

    private function gameData(CarbonInterface $at): array
    {
        return [
            'game_date'     => $at->toDateTimeString(),
            'home_team'     => 'Částkov',
            'away_team'     => 'Havřice',
            'location'      => 'Částkov',
            'match_type_id' => 1,
        ];
    }

    public function test_training_cannot_overlap_with_another_training(): void
    {
        $at = now()->addDays(2)->setTime(17, 30);
        Training::create(['training_date' => $at, 'location' => 'Hřiště']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.trainings.store'), [
                'training_date' => $at->copy()->addHour()->toDateTimeString(),
                'location'      => 'Hřiště',
            ])
            ->assertSessionHasErrors('training_date');

        $this->assertDatabaseCount('trainings', 1);
    }

    public function test_game_cannot_overlap_with_training(): void
    {
        $at = now()->addDays(2)->setTime(17, 30);
        Training::create(['training_date' => $at, 'location' => 'Hřiště']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.games.store'), $this->gameData($at->copy()->subMinutes(90)))
            ->assertSessionHasErrors('game_date');

        $this->assertDatabaseCount('games', 0);
    }

    public function test_event_outside_of_gap_is_allowed(): void
    {
        $at = now()->addDays(2)->setTime(10, 0);
        Game::create($this->gameData($at));

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.trainings.store'), [
                'training_date' => $at->copy()->addHours(3)->toDateTimeString(),
                'location'      => 'Hřiště',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('trainings', 1);
    }

    public function test_invalid_date_reports_only_date_error(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.trainings.store'), ['training_date' => 'nesmysl', 'location' => 'Hřiště'])
            ->assertSessionHasErrors('training_date');

        // Díky 'bail' se NoScheduleConflict po chybě pravidla 'date' už nespustí
        $this->assertCount(1, session('errors')->get('training_date'));
    }
}
