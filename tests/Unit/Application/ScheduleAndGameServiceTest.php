<?php

namespace Tests\Unit\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Sokol\Application\Dto\GameData;
use Sokol\Application\Dto\GameTypeData;
use Sokol\Application\Services\GameService;
use Sokol\Application\Services\GameTypeService;
use Sokol\Application\Services\ScheduleService;
use Sokol\Application\Services\VoterListBuilder;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Exceptions\DomainRuleViolation;
use Tests\Fakes\FixedClock;
use Tests\Fakes\FixedClubSettings;
use Tests\Fakes\InMemoryAttendanceRepository;
use Tests\Fakes\InMemoryGameRepository;
use Tests\Fakes\InMemoryGameTypeRepository;
use Tests\Fakes\InMemoryTrainingRepository;
use Tests\Fakes\InMemoryUserRepository;

class ScheduleAndGameServiceTest extends TestCase
{
    private InMemoryTrainingRepository $trainings;

    private InMemoryGameRepository $games;

    private GameTypeService $types;

    private GameService $gameService;

    protected function setUp(): void
    {
        $this->trainings   = new InMemoryTrainingRepository();
        $this->games       = new InMemoryGameRepository();
        $typeRepository    = new InMemoryGameTypeRepository($this->games);
        $this->types       = new GameTypeService($typeRepository);
        $this->gameService = new GameService(
            $this->games,
            $typeRepository,
            new VoterListBuilder(new InMemoryAttendanceRepository(), new InMemoryUserRepository()),
            new FixedClubSettings('Částkov'),
            new FixedClock(new DateTimeImmutable('2026-11-18 12:00')),
        );
    }

    public function test_events_must_be_two_hours_apart(): void
    {
        $schedule = new ScheduleService($this->trainings, $this->games);
        $training = $this->trainings->save(Training::plan(new DateTimeImmutable('2026-11-20 17:30'), 'Hřiště', null));

        $this->assertTrue($schedule->hasConflict(new DateTimeImmutable('2026-11-20 18:30')));
        $this->assertFalse($schedule->hasConflict(new DateTimeImmutable('2026-11-20 20:00')));
        $this->assertFalse($schedule->hasConflict(new DateTimeImmutable('2026-11-20 18:30'), EventType::Training, $training->id()));
    }

    public function test_game_view_knows_home_team_and_type(): void
    {
        $type = $this->types->create(new GameTypeData('Mistrovské utkání'));

        $game = $this->gameService->schedule(new GameData($type->id, 'Bílovice', 'Částkov', new DateTimeImmutable('2026-11-22 10:15'), 'Bílovice'));

        $this->assertFalse($game->isHome);
        $this->assertTrue($game->awayIsOurs);
        $this->assertSame('Mistrovské utkání', $game->typeName);
        $this->assertFalse($game->isPast);
    }

    public function test_used_game_type_cannot_be_deleted(): void
    {
        $type = $this->types->create(new GameTypeData('Pohár'));
        $this->gameService->schedule(new GameData($type->id, 'A', 'B', new DateTimeImmutable('2026-11-22 10:15')));

        $this->expectException(DomainRuleViolation::class);

        $this->types->delete($type->id);
    }
}
