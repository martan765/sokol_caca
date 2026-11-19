<?php

namespace Tests\Unit\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Sokol\Application\Implementation\DashboardService;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\AttendanceStatusId;
use Sokol\Domain\Enums\EventType;
use Tests\Fakes\InMemoryAttendanceRepository;
use Tests\Fakes\InMemoryGameRepository;
use Tests\Fakes\InMemoryTrainingRepository;
use Tests\Fakes\InMemoryUserRepository;

class DashboardServiceTest extends TestCase
{
    public function test_monthly_attendance_statistics(): void
    {
        $users      = new InMemoryUserRepository();
        $trainings  = new InMemoryTrainingRepository();
        $attendance = new InMemoryAttendanceRepository();
        $service    = new DashboardService($trainings, new InMemoryGameRepository(), $attendance, $users);

        $player = $users->save(new User(null, 'Petr', 'petr@example.com', 'x'));
        $first  = $trainings->save(Training::plan(new DateTimeImmutable('2026-10-05 17:30'), 'Hřiště', null));
        $trainings->save(Training::plan(new DateTimeImmutable('2026-10-12 17:30'), 'Hřiště', null));
        $trainings->save(Training::plan(new DateTimeImmutable('2026-11-02 17:30'), 'Hřiště', null));
        $attendance->setStatus($player->id(), EventType::Training, $first->id(), AttendanceStatusId::Attending->value);

        $months = $service->forPlayer($player->id())->months;

        $this->assertSame(['2026-11', '2026-10'], array_map(fn ($m) => $m->month, $months));
        $this->assertSame(2, $months[1]->stats['trainings']->total);
        $this->assertSame(1, $months[1]->stats['trainings']->attended);
        $this->assertSame(50, $months[1]->stats['trainings']->percentage);
        $this->assertSame(AttendanceStatusId::Attending->value, $months[1]->events[0]->statusId);

        $admin = $service->forAdmin();
        $this->assertSame(2, $admin->months[1]->eventCount);
        $this->assertSame('Petr', $admin->months[1]->players[0]->name);
    }
}
