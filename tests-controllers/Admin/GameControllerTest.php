<?php

namespace Sokol\ControllerTests\Admin;

use App\Http\Controllers\Admin\GameController;
use App\Http\Requests\Admin\GameRequest;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Sokol\Application\Ports\Clock;
use Sokol\Application\Ports\ClubSettings;
use Sokol\Application\Ports\UnitOfWork;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Entities\GameType;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Sokol\Domain\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Domain\Repositories\GameRepositoryInterface;
use Sokol\Domain\Repositories\GameTypeRepositoryInterface;
use Sokol\Domain\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Repositories\UserRepositoryInterface;
use Tests\Fakes\FixedClock;
use Tests\Fakes\FixedClubSettings;
use Tests\Fakes\InMemoryAttendanceRepository;
use Tests\Fakes\InMemoryAttendanceStatusRepository;
use Tests\Fakes\InMemoryGameRepository;
use Tests\Fakes\InMemoryGameTypeRepository;
use Tests\Fakes\InMemoryTrainingRepository;
use Tests\Fakes\InMemoryUserRepository;
use Tests\Fakes\RecordingUnitOfWork;

class GameControllerTest extends TestCase
{
    private InMemoryGameRepository $games;

    private InMemoryTrainingRepository $trainings;

    private int $typeId;

    private MockInterface $logger;

    private GameController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->games     = new InMemoryGameRepository();
        $this->trainings = new InMemoryTrainingRepository();
        $types           = new InMemoryGameTypeRepository($this->games);
        $this->typeId    = $types->save(GameType::create('Mistrovské utkání'))->id();

        $this->app->instance(GameRepositoryInterface::class, $this->games);
        $this->app->instance(GameTypeRepositoryInterface::class, $types);
        $this->app->instance(TrainingRepositoryInterface::class, $this->trainings);
        $this->app->instance(AttendanceRepositoryInterface::class, new InMemoryAttendanceRepository());
        $this->app->instance(AttendanceStatusRepositoryInterface::class, new InMemoryAttendanceStatusRepository());
        $this->app->instance(UserRepositoryInterface::class, new InMemoryUserRepository());
        $this->app->instance(UnitOfWork::class, new RecordingUnitOfWork());
        $this->app->instance(Clock::class, new FixedClock(new DateTimeImmutable('2026-11-18 12:00')));
        $this->app->instance(ClubSettings::class, new FixedClubSettings('Částkov'));

        $this->logger = Mockery::spy(LoggerInterface::class);
        $this->app->when(GameController::class)->needs(LoggerInterface::class)->give(fn () => $this->logger);

        $this->controller = $this->app->make(GameController::class);
    }

    public function test_index_lists_games_from_repository(): void
    {
        $this->games->save(Game::schedule($this->typeId, 'Částkov', 'Havřice', new DateTimeImmutable('2026-11-22 10:15'), 'Částkov', null));

        $view  = $this->controller->index();
        $games = $view->getData()['games'];

        $this->assertSame('admin.games.index', $view->name());
        $this->assertSame(1, $games->total());
        $this->assertSame('Částkov vs Havřice', $games->items()[0]->label);
        $this->assertSame('Mistrovské utkání', $games->items()[0]->typeName);
    }

    public function test_store_saves_game_through_service_and_logs_it(): void
    {
        $request = $this->gameRequest('POST', '/admin/games', $this->formData('2026-11-22 10:15'));

        $response = $this->controller->store($request);

        $this->assertCount(1, $this->games->games());
        $this->assertSame(route('admin.games.index'), $response->getTargetUrl());
        $this->logger->shouldHaveReceived('info')->with('Zápas vytvořen', Mockery::on(
            fn (array $context) => $context['match'] === 'Částkov vs Havřice'
        ));
    }

    public function test_store_rejects_game_colliding_with_training(): void
    {
        $this->trainings->save(Training::plan(new DateTimeImmutable('2026-11-22 10:00'), 'Hřiště', null));

        $this->expectException(ValidationException::class);

        $this->gameRequest('POST', '/admin/games', $this->formData('2026-11-22 11:00'));
    }

    public function test_update_logs_changed_fields(): void
    {
        $game = $this->games->save(Game::schedule($this->typeId, 'Částkov', 'Havřice', new DateTimeImmutable('2026-11-10 10:15'), 'Částkov', null));

        $request = $this->gameRequest('PUT', '/admin/games/' . $game->id(), $this->formData('2026-11-10 10:15', '3:1'), 'admin/games/{game}');
        $this->controller->update($request, $game->id());

        $this->assertSame('3:1', $this->games->getById($game->id())->result());
        $this->logger->shouldHaveReceived('info')->with('Zápas upraven', ['game_id' => $game->id(), 'changes' => ['result' => '3:1']]);
    }

    public function test_destroy_removes_game(): void
    {
        $game = $this->games->save(Game::schedule($this->typeId, 'Částkov', 'Havřice', new DateTimeImmutable('2026-11-22 10:15'), 'Částkov', null));

        $this->controller->destroy($game->id());

        $this->assertFalse($this->games->exists($game->id()));
        $this->logger->shouldHaveReceived('info')->with('Zápas smazán', Mockery::any());
    }

    public function test_edit_of_missing_game_fails(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->controller->edit(999);
    }

    private function formData(string $date, ?string $result = null): array
    {
        return [
            'game_date'     => $date,
            'home_team'     => 'Částkov',
            'away_team'     => 'Havřice',
            'location'      => 'Částkov',
            'match_type_id' => $this->typeId,
            'result'        => $result,
        ];
    }

    private function gameRequest(string $method, string $uri, array $data, ?string $routeUri = null): GameRequest
    {
        $request = GameRequest::create($uri, $method, $data);
        $request->setContainer($this->app)->setRedirector($this->app->make(Redirector::class));

        if ($routeUri !== null) {
            $route = (new Route($method, $routeUri, []))->bind($request);
            $request->setRouteResolver(fn () => $route);
        }

        $request->validateResolved();

        return $request;
    }
}
