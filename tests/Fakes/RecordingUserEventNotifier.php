<?php

namespace Tests\Fakes;

use Sokol\Domain\Interfaces\Managers\UserEventNotifierInterface;

final class RecordingUserEventNotifier implements UserEventNotifierInterface
{
    public array $events = [];

    public function registered(int $userId): void
    {
        $this->events[] = ['registered', $userId];
    }

    public function passwordReset(int $userId): void
    {
        $this->events[] = ['passwordReset', $userId];
    }

    public function emailVerified(int $userId): void
    {
        $this->events[] = ['emailVerified', $userId];
    }

    public function sendEmailVerification(int $userId): void
    {
        $this->events[] = ['sendEmailVerification', $userId];
    }
}
