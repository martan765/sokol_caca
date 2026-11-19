<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin;
use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class AdminLoggingTest extends TestCase
{
    use RefreshDatabase;

    private function spyLoggerFor(string $controller): MockInterface
    {
        $logger = Mockery::spy(LoggerInterface::class);
        $this->app->when($controller)->needs(LoggerInterface::class)->give(fn () => $logger);

        return $logger;
    }

    public function test_admin_controllers_get_admin_channel_logger(): void
    {
        $this->assertSame(
            Log::channel('admin'),
            (fn () => $this->logger)->call($this->app->make(Admin\TrainingController::class)),
        );
    }

    public function test_training_changes_are_logged_with_admin_context(): void
    {
        $logger   = $this->spyLoggerFor(Admin\TrainingController::class);
        $admin    = User::factory()->admin()->create();
        $training = Training::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);

        $this->actingAs($admin)->put(route('admin.trainings.update', $training), [
            'training_date' => $training->training_date->toDateTimeString(),
            'location'      => 'Umělka',
        ]);
        $this->actingAs($admin)->delete(route('admin.trainings.destroy', $training));

        $logger->shouldHaveReceived('info')->with('Trénink upraven', Mockery::on(
            fn (array $context) => $context['training_id'] === $training->id && $context['changes']['location'] === 'Umělka'
        ));
        $logger->shouldHaveReceived('info')->with('Trénink smazán', Mockery::any());

        $this->assertSame($admin->email, Context::get('admin'));
    }

    public function test_refused_game_type_deletion_is_logged(): void
    {
        $logger = $this->spyLoggerFor(Admin\GameTypeController::class);
        $admin  = User::factory()->admin()->create();
        $game   = Game::create(['match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDay()]);

        $this->actingAs($admin)->delete(route('admin.game-types.destroy', $game->match_type_id));

        $logger->shouldHaveReceived('notice')->once();
        $logger->shouldNotHaveReceived('info');
    }
}
