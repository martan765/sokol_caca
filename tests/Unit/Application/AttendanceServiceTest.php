<?php

namespace Tests\Unit\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Sokol\Application\Dto\AttendanceSheetRow;
use Sokol\Application\Dto\AttendanceVoteData;
use Sokol\Application\Services\AttendanceService;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\AttendanceStatusId;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Enums\Role;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Tests\Fakes\InMemoryAttendanceRepository;
use Tests\Fakes\InMemoryAttendanceStatusRepository;
use Tests\Fakes\InMemoryGameRepository;
use Tests\Fakes\InMemoryTrainingRepository;
use Tests\Fakes\InMemoryUserRepository;
use Tests\Fakes\RecordingUnitOfWork;

class AttendanceServiceTest extends TestCase
{
    private InMemoryUserRepository $users;

    private InMemoryTrainingRepository $trainings;

    private RecordingUnitOfWork $unitOfWork;

    private AttendanceService $service;

    protected function setUp(): void
    {
        $this->users      = new InMemoryUserRepository();
        $this->trainings  = new InMemoryTrainingRepository();
        $this->unitOfWork = new RecordingUnitOfWork();
        $this->service    = new AttendanceService(
            new InMemoryAttendanceRepository(),
            new InMemoryAttendanceStatusRepository(),
            $this->users,
            $this->trainings,
            new InMemoryGameRepository(),
            $this->unitOfWork,
        );
    }

    public function test_sheet_is_saved_in_unit_of_work_only_for_players(): void
    {
        $coach    = $this->users->save(new User(null, 'Trenér', 'admin@example.com', 'x', Role::Admin));
        $a        = $this->users->save(new User(null, 'Adam', 'a@example.com', 'x'));
        $b        = $this->users->save(new User(null, 'Bohouš', 'b@example.com', 'x'));
        $training = $this->trainings->save(Training::plan(new DateTimeImmutable('+1 day'), 'Hřiště', null));

        $this->service->vote(new AttendanceVoteData($coach->id(), EventType::Training, $training->id(), AttendanceStatusId::Attending->value));
        $this->service->vote(new AttendanceVoteData($b->id(), EventType::Training, $training->id(), AttendanceStatusId::Attending->value));

        $this->service->saveSheet(EventType::Training, $training->id(), [
            new AttendanceSheetRow($a->id(), AttendanceStatusId::NotAttending->value, 'nemocný'),
            new AttendanceSheetRow($b->id(), null),
            new AttendanceSheetRow(999, AttendanceStatusId::Attending->value),
        ]);

        $current = $this->service->sheet(EventType::Training, $training->id())->current;

        $this->assertSame(1, $this->unitOfWork->transactions);
        $this->assertSame('nemocný', $current[$a->id()]->note);
        $this->assertArrayNotHasKey($b->id(), $current);
        $this->assertArrayHasKey($coach->id(), $current);
        $this->assertArrayNotHasKey(999, $current);
    }

    public function test_sheet_for_missing_event_fails(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->service->saveSheet(EventType::Training, 42, []);
    }

    public function test_status_can_be_renamed(): void
    {
        $changes = $this->service->renameStatus(AttendanceStatusId::Attending->value, 'Přijdu');

        $this->assertSame(['name' => 'Přijdu'], $changes);
        $this->assertSame('Přijdu', $this->service->statuses()[0]->name);
    }
}
