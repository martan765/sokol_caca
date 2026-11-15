<?php

namespace Sokol\Application\Services;

use Sokol\Application\ViewModels\VoterView;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\UserRepositoryInterface;

class VoterListBuilder
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function forEvents(EventType $type, array $eventIds, ?int $viewerId): array
    {
        $votesByEvent = $this->attendance->ofEvents($type, $eventIds);

        $userIds = [];
        foreach ($votesByEvent as $votes) {
            foreach ($votes as $vote) {
                $userIds[$vote->userId()] = $vote->userId();
            }
        }
        $users = $this->users->findByIds(array_values($userIds));

        $result = [];
        foreach ($eventIds as $eventId) {
            $voters = [];
            $myStatusId = null;

            foreach ($votesByEvent[$eventId] ?? [] as $vote) {
                $user = $users[$vote->userId()] ?? null;
                if ($user === null) {
                    continue;
                }

                $voters[$vote->statusId()][] = new VoterView((int) $user->id(), $user->name());

                if ($vote->userId() === $viewerId) {
                    $myStatusId = $vote->statusId();
                }
            }

            $result[$eventId] = ['voters' => $voters, 'myStatusId' => $myStatusId];
        }

        return $result;
    }
}
