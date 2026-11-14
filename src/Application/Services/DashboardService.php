<?php

namespace Sokol\Application\Services;

use DateTimeImmutable;
use Sokol\Application\ViewModels\AdminMonthView;
use Sokol\Application\ViewModels\AdminPlayerMonthView;
use Sokol\Application\ViewModels\AttendanceStatsView;
use Sokol\Application\ViewModels\AttendanceView;
use Sokol\Application\ViewModels\DashboardAdminView;
use Sokol\Application\ViewModels\DashboardEventView;
use Sokol\Application\ViewModels\DashboardMonthView;
use Sokol\Application\ViewModels\DashboardPlayerView;
use Sokol\Domain\Entities\Attendance;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Enums\AttendanceStatusId;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Repositories\GameRepositoryInterface;
use Sokol\Domain\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Repositories\UserRepositoryInterface;

/**
 * Měsíční přehled akcí a docházkové statistiky pro dashboard.
 */
class DashboardService
{
    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
        private readonly GameRepositoryInterface $games,
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function forPlayer(int $userId): DashboardPlayerView
    {
        $own = [
            EventType::Training->value => $this->attendance->ofUser($userId, EventType::Training),
            EventType::Game->value     => $this->attendance->ofUser($userId, EventType::Game),
        ];

        $months = [];
        foreach ($this->eventsByMonth() as $month => $events) {
            $events = array_map(
                fn (DashboardEventView $e) => $e->withAttendance($this->view($own[$e->type][$e->id] ?? null)),
                $events,
            );

            $months[] = new DashboardMonthView($month, $this->monthStart($month), $events, $this->monthStats($events));
        }

        return new DashboardPlayerView($months);
    }

    public function forAdmin(): DashboardAdminView
    {
        $players = $this->users->players();
        $byUser  = [
            EventType::Training->value => $this->attendance->byUser(EventType::Training),
            EventType::Game->value     => $this->attendance->byUser(EventType::Game),
        ];

        $months = [];
        foreach ($this->eventsByMonth() as $month => $events) {
            $rows = [];
            foreach ($players as $player) {
                $playerId = (int) $player->id();
                $playerEvents = array_map(
                    fn (DashboardEventView $e) => $e->withAttendance($this->view($byUser[$e->type][$playerId][$e->id] ?? null)),
                    $events,
                );

                $rows[] = new AdminPlayerMonthView($playerId, $player->name(), $this->monthStats($playerEvents), $playerEvents);
            }

            $months[] = new AdminMonthView($month, $this->monthStart($month), count($events), $rows);
        }

        return new DashboardAdminView($months);
    }

    private function eventsByMonth(): array
    {
        $events = [];

        foreach ($this->trainings->all() as $t) {
            $events[] = new DashboardEventView(EventType::Training->value, (int) $t->id(), $t->date(), 'Trénink', $t->location());
        }

        foreach ($this->games->all() as $g) {
            $events[] = new DashboardEventView(EventType::Game->value, (int) $g->id(), $g->date(), $g->label(), $g->location());
        }

        usort($events, fn (DashboardEventView $a, DashboardEventView $b) => $b->date <=> $a->date);

        $byMonth = [];
        foreach ($events as $event) {
            $byMonth[$event->date->format('Y-m')][] = $event;
        }

        return array_map(function (array $monthEvents) {
            usort($monthEvents, fn (DashboardEventView $a, DashboardEventView $b) => $a->date <=> $b->date);

            return $monthEvents;
        }, $byMonth);
    }

    private function monthStats(array $events): array
    {
        $stats = [];

        foreach ([EventType::Training->value => 'trainings', EventType::Game->value => 'games'] as $type => $key) {
            $ofType   = array_filter($events, fn (DashboardEventView $e) => $e->type === $type);
            $attended = array_filter($ofType, fn (DashboardEventView $e) => $e->statusId === AttendanceStatusId::Attending->value);

            $stats[$key] = AttendanceStatsView::of(count($ofType), count($attended));
        }

        return $stats;
    }

    private function monthStart(string $month): DateTimeImmutable
    {
        return new DateTimeImmutable($month . '-01');
    }

    private function view(?Attendance $attendance): ?AttendanceView
    {
        return $attendance ? AttendanceView::fromEntity($attendance) : null;
    }
}
